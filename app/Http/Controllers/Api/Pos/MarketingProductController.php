<?php

namespace App\Http\Controllers\Api\Pos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\MarketingProductRequest;
use App\Http\Resources\Pos\MarketingProductResource;
use App\Models\PosMarketingProduct;
use Illuminate\Http\Request;

class MarketingProductController extends Controller
{
    protected array $sortableColumns = ['created_at', 'product_name'];

    public function index(Request $request)
    {
        $orderByKey   = in_array($request->input('order_by_key', 'created_at'), $this->sortableColumns)
                            ? $request->input('order_by_key', 'created_at')
                            : 'created_at';
        $orderByValue = strtoupper($request->input('order_by_value', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

        $marketingProducts = PosMarketingProduct::with(['product', 'createdBy', 'marketing'])
            ->join('products', 'products.id', '=', 'marketing_products.product_id')
            ->select('marketing_products.*', 'products.name as product_name') // hindari conflict kolom
            ->when($request->marketing_uuid, function ($query, $marketingUuid) {
                $query->whereHas('marketing', fn($q) =>
                    $q->where('uuid', $marketingUuid)
                );
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(products.name) LIKE ?', ["%" . strtolower($search) . "%"])
                    ->orWhereRaw('LOWER(products.code) LIKE ?', ["%" . strtolower($search) . "%"]);
                });
            })
            ->orderBy(
                $orderByKey === 'product_name' ? 'products.name' : $orderByKey,
                $orderByValue
            )
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('marketing_product.list'),
            'data'    => MarketingProductResource::collection($marketingProducts),
        ]);
    }

    public function store(MarketingProductRequest $request)
    {
        $marketingProduct = PosMarketingProduct::create([
            'product_id'      => $request->getProductId(),
            'marketing_id'    => $request->getMarketingId(),
            'marketing_price' => $request->marketing_price,
            'created_by'      => $request->user()->id,
            'company_id'      => $request->user()->company_id,
        ]);

        $marketingProduct->load(['product', 'createdBy', 'marketing']);

        return response()->json([
            'success' => true,
            'message' => __('marketing_product.stored'),
            'data'    => new MarketingProductResource($marketingProduct->load('product')),
        ], 201);
    }

    public function show(PosMarketingProduct $marketingProduct)
    {
        $marketingProduct->loadMissing(['product', 'createdBy', 'marketing']);

        return response()->json([
            'success' => true,
            'message' => __('marketing_product.detail'),
            'data'    => new MarketingProductResource($marketingProduct->load('product')),
        ]);
    }

    public function update(MarketingProductRequest $request, PosMarketingProduct $marketingProduct)
    {
        $marketingProduct->update(
            $request->only(['marketing_price'])
        );

        $marketingProduct->load(['product', 'createdBy', 'marketing']);

        return response()->json([
            'success' => true,
            'message' => __('marketing_product.updated'),
            'data'    => new MarketingProductResource($marketingProduct->load('product')),
        ]);
    }

    public function destroy(PosMarketingProduct $marketingProduct)
    {
        $marketingProduct->delete();

        return response()->json([
            'success' => true,
            'message' => __('marketing_product.deleted'),
        ]);
    }
}
