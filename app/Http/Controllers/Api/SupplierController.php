<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected array $sortableColumns = ['name', 'created_at'];

    public function index(Request $request)
    {
        $orderByKey = in_array($request->input('order_by_key', 'name'), $this->sortableColumns)
            ? $request->input('order_by_key', 'name')
            : 'name';
        $orderByValue = strtoupper($request->input('order_by_value', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $suppliers = Supplier::query()
            ->with(['createdBy'])
            ->when($request->search, function ($query, $search) {
                // Case-insensitive search using LOWER() for PostgreSQL and MySQL compatibility
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->orderBy($orderByKey, $orderByValue)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('suppliers.list'),
            'data' => SupplierResource::collection($suppliers),
        ]);
    }

    public function store(SupplierRequest $request)
    {
        $supplier = Supplier::create([
            'name'       => $request->name,
            'address'    => $request->address,
            'phone'      => $request->phone,
            'created_by' => $request->user()->id,
            'company_id' => $request->user()->company_id,
        ]);

        $supplier->load('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('suppliers.stored'),
            'data'    => new SupplierResource($supplier),
        ], 201);
    }

    public function show(Supplier $supplier)
    {
        $supplier->loadMissing('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('suppliers.detail'),
            'data'    => new SupplierResource($supplier),
        ]);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update(array_filter([
            'name'    => $request->has('name') ? $request->name : null,
            'address' => $request->has('address') ? $request->address : null,
            'phone'   => $request->has('phone') ? $request->phone : null,
        ], fn($value) => !is_null($value)));

        $supplier->load('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('suppliers.updated'),
            'data'    => new SupplierResource($supplier),
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchaseTransactions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('suppliers.has_purchases'),
                'code'    => 422,
            ], 422);
        }

        $supplier->delete();

        return response()->json([
            'success' => true,
            'message' => __('suppliers.deleted'),
        ]);
    }
}