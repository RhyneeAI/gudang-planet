<?php

namespace App\Http\Controllers\Api\Operational;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operational\OpsMandorStoreRequest;
use App\Http\Resources\Operational\OpsMandorResource;
use App\Http\Resources\Operational\OpsSubCompanyResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OpsMandorController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : today()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : today()->endOfDay();

        $mandors = User::where('role', Role::MANDOR)
            ->where('is_active', true)
            ->when($request->boolean('is_dashboard_data'), function ($query) use ($dateFrom, $dateTo) {
                $query
                    ->withSum([
                        'mandorIncomes as total_income' => function (Builder $q) use ($dateFrom, $dateTo) {
                            $q->whereBetween('date', [$dateFrom, $dateTo]);
                        }
                    ], 'amount')
                    ->withSum([
                        'mandorExpenses as total_expense' => function (Builder $q) use ($dateFrom, $dateTo) {
                            $q->whereBetween('date', [$dateFrom, $dateTo]);
                        }
                    ], 'amount');
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(phone) LIKE ?', ['%' . strtolower($search) . '%']);
                });
            })
            ->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('operational.mandors.list'),
            'data' => OpsMandorResource::collection($mandors),
        ]);
    }

    public function store(OpsMandorStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $randomDigits = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
            $rawPassword = strtolower(str_replace(' ', '', $request->name)) . $randomDigits;

            $mandor = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($rawPassword),
                'address' => $request->address,
                'role' => Role::MANDOR,
                'is_active' => true,
                'company_id' => $request->user()->company_id,
            ]);

            DB::commit();

            $mandor->load(['opsSubCompanies.wallet']);

            return response()->json([
                'success' => true,
                'message' => __('operational.mandors.stored'),
                'data' => new OpsMandorResource($mandor),
                'sub_company' => new OpsSubCompanyResource($mandor->opsSubCompanies->first()),
                'credentials' => [
                    'phone' => $mandor->phone,
                    'password' => $rawPassword,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
