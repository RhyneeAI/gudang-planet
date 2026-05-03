<?php

namespace App\Http\Controllers\Api;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\MarketingRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MarketingController extends Controller
{
    protected array $sortableColumns = ['name', 'username', 'email', 'created_at'];

    public function index(Request $request)
    {
        $orderByKey   = in_array($request->input('order_by_key', 'name'), $this->sortableColumns)
                            ? $request->input('order_by_key', 'name')
                            : 'name';
        $orderByValue = strtoupper($request->input('order_by_value', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $marketings = User::where('role', ROLE::MARKETING)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($orderByKey, $orderByValue)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('marketings.list'),
            'data'    => UserResource::collection($marketings),
        ]);
    }

    public function store(MarketingRequest $request)
    {
        $baseUsername = strtolower(str_replace(' ', '', $request->name));
        $username     = $this->generateUniqueUsername($baseUsername);

        $randomDigits = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        $rawPassword  = strtolower(str_replace(' ', '', $request->name)) . $randomDigits;

        $marketing = User::create([
            'name'       => $request->name,
            'username'   => $username,
            'email'      => $request->email,
            'password'   => Hash::make($rawPassword),
            'address'    => $request->address,
            'phone'      => $request->phone,
            'role'       => Role::MARKETING,
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('marketings.stored'),
            'data'    => new UserResource($marketing),
            'credentials' => [
                'username' => $username,
                'password' => $rawPassword,
            ],
        ], 201);
    }

    private function generateUniqueUsername(string $base): string
    {
        $username = $base;
        $counter  = 1;

        // Jika username sudah ada, tambah angka di belakang
        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    public function show(User $marketing)
    {
        return response()->json([
            'success' => true,
            'message' => __('marketings.detail'),
            'data'    => new UserResource($marketing),
        ]);
    }

    public function update(MarketingRequest $request, User $marketing)
    {
        $data = $request->only(['name', 'username', 'email', 'address', 'phone']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $marketing->update($data);

        return response()->json([
            'success' => true,
            'message' => __('marketings.updated'),
            'data'    => new UserResource($marketing),
        ]);
    }

    public function destroy(User $marketing)
    {
        $hasProducts     = $marketing->marketingProducts()->exists();
        $hasTransactions = $marketing->salesTransactions()->exists();

        if ($hasProducts || $hasTransactions) {
            return response()->json([
                'success' => false,
                'message' => __('marketings.has_relations'),
                'code'    => 422,
            ], 422);
        }

        $marketing->delete();

        return response()->json([
            'success' => true,
            'message' => __('marketings.deleted'),
        ]);
    }
}