<?php

namespace App\Http\Controllers\Api\Absence;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Absence\AbsEmployeeRequest;
use App\Http\Resources\Absence\AbsEmployeeResource;
use App\Models\AbsBranch;
use App\Models\AbsEmployeeProfile;
use App\Models\AbsShift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AbsEmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = User::where('role', Role::KARYAWAN)
            ->with(['absEmployeeProfile.branch', 'absEmployeeProfile.shift'])
            ->when($request->branch_uuid, fn ($q, $uuid) =>
                $q->whereHas('absEmployeeProfile.branch', fn ($b) => $b->where('uuid', $uuid))
            )
            ->when($request->has('is_active'), fn ($q) => $q->where('is_active', $request->boolean('is_active')))
            ->when($request->search, function ($query, $search) {
                $term = '%' . strtolower($search) . '%';
                $query->where(function ($inner) use ($term) {
                    $inner->whereRaw('LOWER(name) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(phone) LIKE ?', [$term]);
                });
            })
            ->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('absence.employees.list'),
            'data' => AbsEmployeeResource::collection($employees),
        ]);
    }

    public function store(AbsEmployeeRequest $request)
    {
        DB::beginTransaction();

        try {
            $branch = AbsBranch::where('uuid', $request->branch_uuid)->firstOrFail();
            $shift = AbsShift::where('uuid', $request->shift_uuid)->firstOrFail();

            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => Role::KARYAWAN,
                'is_active' => true,
                'company_id' => $request->user()->company_id,
                'created_by' => $request->user()->id,
            ]);

            AbsEmployeeProfile::create([
                'user_id' => $user->id,
                'abs_branch_id' => $branch->id,
                'abs_shift_id' => $shift->id,
                'daily_rate' => $request->daily_rate,
                'company_id' => $request->user()->company_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('absence.employees.stored'),
                'data' => new AbsEmployeeResource(
                    $user->load(['absEmployeeProfile.branch', 'absEmployeeProfile.shift'])
                ),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(User $absEmployee)
    {
        $this->ensureKaryawan($absEmployee);

        return response()->json([
            'success' => true,
            'message' => __('absence.employees.detail'),
            'data' => new AbsEmployeeResource(
                $absEmployee->load(['absEmployeeProfile.branch', 'absEmployeeProfile.shift'])
            ),
        ]);
    }

    public function update(AbsEmployeeRequest $request, User $absEmployee)
    {
        $this->ensureKaryawan($absEmployee);

        DB::beginTransaction();

        try {
            $branch = AbsBranch::where('uuid', $request->branch_uuid)->firstOrFail();
            $shift = AbsShift::where('uuid', $request->shift_uuid)->firstOrFail();

            $updateData = [
                'name' => $request->name,
                'phone' => $request->phone,
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->boolean('is_active');
            }

            $absEmployee->update($updateData);

            $absEmployee->absEmployeeProfile()->updateOrCreate(
                ['user_id' => $absEmployee->id],
                [
                    'abs_branch_id' => $branch->id,
                    'abs_shift_id' => $shift->id,
                    'daily_rate' => $request->daily_rate,
                    'company_id' => $absEmployee->company_id,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('absence.employees.updated'),
                'data' => new AbsEmployeeResource(
                    $absEmployee->fresh()->load(['absEmployeeProfile.branch', 'absEmployeeProfile.shift'])
                ),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function destroy(User $absEmployee)
    {
        $this->ensureKaryawan($absEmployee);
        $absEmployee->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => __('absence.employees.deactivated'),
        ]);
    }

    public function resetPassword(Request $request, User $absEmployee)
    {
        $this->ensureKaryawan($absEmployee);

        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $absEmployee->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => __('absence.employees.password_reset'),
        ]);
    }

    protected function ensureKaryawan(User $user): void
    {
        if ($user->role !== Role::KARYAWAN) {
            abort(response()->json([
                'success' => false,
                'message' => 'Not found.',
                'code' => 404,
            ], 404));
        }
    }
}
