<?php

namespace App\Http\Controllers\Api\Operational;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Resources\Operational\OpsSubCompanyResource;
use App\Models\OpsSubCompany;
use Illuminate\Http\Request;

class OpsSubCompanyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $subCompanies = OpsSubCompany::with(['mandor', 'createdBy', 'wallet'])
            ->when($user->role === Role::MANDOR, fn ($query) => $query->where('mandor_id', $user->id))
            ->when(
                $request->mandor_uuid && $user->role !== Role::MANDOR,
                fn ($query) => $query->whereHas(
                    'mandor',
                    fn ($mandorQuery) => $mandorQuery->where('uuid', $request->mandor_uuid)
                )
            )
            ->when($request->has('is_active'), fn ($query) => $query->where('is_active', $request->boolean('is_active')))
            ->when($request->search, function ($query, $search) {
                $query->where(function ($innerQuery) use ($search) {
                    $term = '%' . strtolower($search) . '%';
                    $innerQuery->whereRaw('LOWER(name) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(code) LIKE ?', [$term]);
                });
            })
            ->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('operational.sub_companies.list'),
            'data' => OpsSubCompanyResource::collection($subCompanies),
        ]);
    }

    public function show(OpsSubCompany $opsSubCompany)
    {
        $this->authorizeSubCompanyAccess($opsSubCompany);

        return response()->json([
            'success' => true,
            'message' => __('operational.sub_companies.detail'),
            'data' => new OpsSubCompanyResource(
                $opsSubCompany->load(['mandor', 'createdBy', 'wallet'])
            ),
        ]);
    }

    protected function authorizeSubCompanyAccess(OpsSubCompany $subCompany): void
    {
        $user = request()->user();

        if ($user->role === Role::MANDOR && $subCompany->mandor_id !== $user->id) {
            abort(response()->json([
                'success' => false,
                'message' => 'You don\'t have permission to access this resource.',
                'code' => 403,
            ], 403));
        }
    }
}
