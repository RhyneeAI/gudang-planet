<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected array $sortableColumns = ['name', 'created_at'];

    public function index(Request $request)
    {
        $orderByKey = in_array($request->input('order_by_key', 'name'), $this->sortableColumns)
            ? $request->input('order_by_key', 'name')
            : 'name';
        $orderByValue = strtoupper($request->input('order_by_value', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $units = Unit::query()
            ->with(['createdBy'])
            ->when($request->search, function ($query, $search) {
                // Case-insensitive search using LOWER() for PostgreSQL and MySQL compatibility
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->orderBy($orderByKey, $orderByValue)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('units.list'),
            'data' => UnitResource::collection($units),
        ]);
    }

    public function store(UnitRequest $request)
    {
        $unit = Unit::create([
            'name'       => $request->name,
            'created_by' => $request->user()->id,
            'company_id' => $request->user()->company_id,
        ]);

        $unit->load('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('units.stored'),
            'data'    => new UnitResource($unit),
        ], 201);
    }

    public function show(Unit $unit)
    {
        $unit->loadMissing('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('units.detail'),
            'data'    => new UnitResource($unit),
        ]);
    }

    public function update(UnitRequest $request, Unit $unit)
    {
        if ($request->has('name')) {
            $unit->update(['name' => $request->name]);
        }

        $unit->load('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('units.updated'),
            'data'    => new UnitResource($unit),
        ]);
    }

    public function destroy(Unit $unit)
    {
        if ($unit->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('units.has_products'),
                'code'    => 422,
            ], 422);
        }

        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => __('units.deleted'),
        ]);
    }
}