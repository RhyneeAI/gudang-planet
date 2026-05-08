<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerTypeRequest;
use App\Http\Resources\CustomerTypeResource;
use App\Models\CustomerType;
use Illuminate\Http\Request;

class CustomerTypeController extends Controller
{
    protected array $sortableColumns = ['type', 'discount', 'created_at'];

    public function index(Request $request)
    {
        $orderByKey   = in_array($request->input('order_by_key', 'type'), $this->sortableColumns)
                            ? $request->input('order_by_key', 'type')
                            : 'type';
        $orderByValue = strtoupper($request->input('order_by_value', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $customerTypes = CustomerType::query()
            ->when($request->search, function ($query, $search) {
                $query->whereRaw('LOWER(type) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->orderBy($orderByKey, $orderByValue)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('customer_types.list'),
            'data'    => CustomerTypeResource::collection($customerTypes),
        ]);
    }

    public function store(CustomerTypeRequest $request)
    {
        $customerType = CustomerType::create([
            'type'       => $request->type,
            'discount'   => $request->discount ?? 0,
            'created_by' => $request->user()->id,
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('customer_types.stored'),
            'data'    => new CustomerTypeResource($customerType),
        ], 201);
    }

    public function show(CustomerType $customerType)
    {
        return response()->json([
            'success' => true,
            'message' => __('customer_types.detail'),
            'data'    => new CustomerTypeResource($customerType),
        ]);
    }

    public function update(CustomerTypeRequest $request, CustomerType $customerType)
    {
        $customerType->update($request->only(['type', 'discount']));

        return response()->json([
            'success' => true,
            'message' => __('customer_types.updated'),
            'data'    => new CustomerTypeResource($customerType),
        ]);
    }

    public function destroy(CustomerType $customerType)
    {
        if ($customerType->customers()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('customer_types.has_customers'),
                'code'    => 422,
            ], 422);
        }

        $customerType->delete();

        return response()->json([
            'success' => true,
            'message' => __('customer_types.deleted'),
        ]);
    }
}