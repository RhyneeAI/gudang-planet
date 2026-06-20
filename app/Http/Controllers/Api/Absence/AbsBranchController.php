<?php

namespace App\Http\Controllers\Api\Absence;

use App\Http\Controllers\Controller;
use App\Http\Requests\Absence\AbsBranchRequest;
use App\Http\Resources\Absence\AbsBranchResource;
use App\Models\AbsBranch;
use Illuminate\Http\Request;

class AbsBranchController extends Controller
{
    public function index(Request $request)
    {
        $branches = AbsBranch::when($request->search, function ($query, $search) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
        })
            ->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('absence.branches.list'),
            'data' => AbsBranchResource::collection($branches),
        ]);
    }

    public function store(AbsBranchRequest $request)
    {
        $branch = AbsBranch::create([
            ...$request->validated(),
            'radius_meter' => $request->input('radius_meter', config('absence.default_radius_meter')),
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('absence.branches.stored'),
            'data' => new AbsBranchResource($branch),
        ], 201);
    }

    public function show(AbsBranch $absBranch)
    {
        return response()->json([
            'success' => true,
            'message' => __('absence.branches.detail'),
            'data' => new AbsBranchResource($absBranch),
        ]);
    }

    public function update(AbsBranchRequest $request, AbsBranch $absBranch)
    {
        $absBranch->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('absence.branches.updated'),
            'data' => new AbsBranchResource($absBranch->fresh()),
        ]);
    }

    public function destroy(AbsBranch $absBranch)
    {
        $absBranch->delete();

        return response()->json([
            'success' => true,
            'message' => __('absence.branches.deleted'),
        ]);
    }
}
