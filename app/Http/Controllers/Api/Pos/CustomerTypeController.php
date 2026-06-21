<?php

namespace App\Http\Controllers\Api\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\CustomerTypeRequest;
use App\Http\Resources\Pos\CustomerTypeResource;
use App\Models\PosCustomerType;
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

        $customerTypes = PosCustomerType::query()
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
        $customerType = PosCustomerType::create([
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

    public function show(PosCustomerType $customerType)
    {
        return response()->json([
            'success' => true,
            'message' => __('customer_types.detail'),
            'data'    => new CustomerTypeResource($customerType),
        ]);
    }

    public function update(CustomerTypeRequest $request, PosCustomerType $customerType)
    {
        $customerType->update($request->only(['type', 'discount']));

        return response()->json([
            'success' => true,
            'message' => __('customer_types.updated'),
            'data'    => new CustomerTypeResource($customerType),
        ]);
    }

    public function destroy(PosCustomerType $customerType)
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
