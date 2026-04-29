<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected array $sortableColumns = ['name', 'phone', 'created_at'];

    public function index(Request $request)
    {
        $orderByKey   = in_array($request->input('order_by_key', 'name'), $this->sortableColumns)
                            ? $request->input('order_by_key', 'name')
                            : 'name';
        $orderByValue = strtoupper($request->input('order_by_value', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $customers = Customer::when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy($orderByKey, $orderByValue)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('customers.list'),
            'data'    => CustomerResource::collection($customers),
        ]);
    }

    public function store(CustomerRequest $request)
    {
        $customer = Customer::create([
            'name'             => $request->name,
            'address'          => $request->address,
            'phone'            => $request->phone,
            'customer_type_id' => $request->customer_type_id,
            'company_id'       => $request->user()->company_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('customers.stored'),
            'data'    => new CustomerResource($customer),
        ], 201);
    }

    public function show(Customer $customer)
    {
        return response()->json([
            'success' => true,
            'message' => __('customers.detail'),
            'data'    => new CustomerResource($customer),
        ]);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $customer->update(array_filter([
            'name'             => $request->has('name') ? $request->name : null,
            'address'          => $request->has('address') ? $request->address : null,
            'phone'            => $request->has('phone') ? $request->phone : null,
            'customer_type_id' => $request->has('customer_type_id') ? $request->customer_type_id : null,
        ], fn($value) => !is_null($value)));

        return response()->json([
            'success' => true,
            'message' => __('customers.updated'),
            'data'    => new CustomerResource($customer),
        ]);
    }

    public function destroy(Customer $customer)
    {
        if ($customer->salesTransactions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('customers.has_transactions'),
                'code'    => 422,
            ], 422);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => __('customers.deleted'),
        ]);
    }
}