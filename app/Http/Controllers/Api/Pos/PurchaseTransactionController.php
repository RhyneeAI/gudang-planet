<?php

namespace App\Http\Controllers\Api\Pos;

use App\Enums\PosInstallmentStatus;
use App\Enums\PosPaymentType;
use App\Enums\PosStockMutationType;
use App\Enums\PosTransactionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pos\PurchaseTransactionRequest;
use App\Http\Resources\Pos\PurchaseTransactionResource;
use App\Models\PosProduct;
use App\Models\PosPurchaseDetail;
use App\Models\PosPurchaseInstallmentPlan;
use App\Models\PosPurchaseTransaction;
use App\Models\PosStockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseTransactionController extends Controller
{
    protected array $sortableColumns = ['transaction_code', 'transaction_date', 'transaction_status', 'total'];

    public function index(Request $request)
    {
        $orderByKey   = in_array($request->input('order_by_key', 'transaction_date'), $this->sortableColumns)
                            ? $request->input('order_by_key', 'transaction_date')
                            : 'transaction_date';
        $orderByValue = strtoupper($request->input('order_by_value', 'DESC')) === 'DESC' ? 'DESC' : 'ASC';

        $transactions = PosPurchaseTransaction::with(['supplier', 'createdBy', 'details', 'details.product'])
            ->when($request->date_from, fn($q, $date) =>
                $q->whereDate('transaction_date', '>=', $date)
            )
            ->when($request->date_to, fn($q, $date) =>
                $q->whereDate('transaction_date', '<=', $date)
            )
            ->when($request->status, fn($q, $status) =>
                $q->where('transaction_status', $status)
            )
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(transaction_code) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                        $supplierQuery->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
                });
            })
            ->when($request->created_by_uuid, fn($q, $uuid) =>
                $q->whereHas('createdBy', fn($u) =>
                    $u->where('uuid', $uuid)
                )
            )
            ->orderBy($orderByKey, $orderByValue)
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('purchase_transactions.list'),
            'data'    => PurchaseTransactionResource::collection($transactions),
        ]);
    }

    public function store(PurchaseTransactionRequest $request)
    {
        DB::beginTransaction();

        try {
            $supplierId = $request->getSupplierId();
            $discount   = $request->discount ?? 0;
            $items      = $request->items;

            $productUuids = collect($items)->pluck('product_uuid');
            $products     = PosProduct::whereIn('uuid', $productUuids)
                ->where('company_id', $request->user()->company_id)
                ->get()
                ->keyBy('uuid');

            // Satu-satunya validasi yang masuk akal di controller
            // karena butuh query + company_id context
            if ($products->count() !== $productUuids->unique()->count()) {
                return response()->json([
                    'success' => false,
                    'message' => __('purchase_transactions.validation.item_product_not_found'),
                    'code'    => 422,
                ], 422);
            }

            $companyCode = $request->user()->company->code;
            $datePrefix = now()->format('Ymd'); 
            $prefix = "PO-{$companyCode}{$datePrefix}";

            $lastTransaction = PosPurchaseTransaction::where('transaction_code', 'like', $prefix . '%')->orderBy('id', 'desc')->first();
            if ($lastTransaction) {
                $lastNumber = (int) substr($lastTransaction->transaction_code, -4);
                $sequence = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $sequence = '001';
            }

            $transactionCode = $prefix . $sequence;

            $transaction = PosPurchaseTransaction::create([
                'transaction_code'   => $transactionCode,
                'transaction_date'   => $request->transaction_date . ' ' . date("H:i:s"),
                'discount'           => $discount,
                'total'              => $request->total,
                'paid'               => $request->paid,
                'payment_type'       => $request->payment_type,
                'transaction_status' => $request->payment_type === PosPaymentType::CICIL->value
                                                                ? PosTransactionStatus::UNPAID  
                                                                : PosTransactionStatus::PAID,
                'supplier_id'        => $supplierId,
                'created_by'         => $request->user()->id,
                'company_id'         => $request->user()->company_id,
            ]);

            foreach ($items as $item) {
                /** @var Product $product */
                $product     = $products->get($item['product_uuid']);
                $subtotal    = $item['quantity'] * $item['buy_price'];
                $stockBefore = $product->stock;
                $stockAfter  = $stockBefore + $item['quantity'];

                $detail = PosPurchaseDetail::create([
                    'purchase_id' => $transaction->id,
                    'product_id'  => $product->id,
                    'quantity'    => $item['quantity'],
                    'buy_price'   => $item['buy_price'],
                    'subtotal'    => $subtotal,
                    'company_id'  => $request->user()->company_id,
                ]);

                $product->update([
                    'stock'                 => $stockAfter,
                    'base_price'            => ($product->base_price + $item['buy_price']) / 2,
                    'last_purchase_price'   => $item['buy_price']
                ]);

                PosStockMutation::create([
                    'type'         => PosStockMutationType::PURCHASE_IN,
                    'quantity'     => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after'  => $stockAfter,
                    'notes'        => "Pembelian #{$transactionCode}",
                    'product_id'   => $product->id,
                    'company_id'   => $request->user()->company_id,
                    'reference_id' => $detail->id,
                    'created_by'   => $request->user()->id,
                ]);
            }

            if ($request->payment_type === PosPaymentType::CICIL->value) {
                PosPurchaseInstallmentPlan::create([
                    'ulid'                      => Str::ulid(),
                    'purchase_transaction_id'   => $transaction->id,
                    'supplier_id'               => $supplierId,
                    'total_amount'              => $request->total,
                    'paid_amount'               => 0,
                    'start_date'                => now()->toDateString(),
                    'status'                    => PosInstallmentStatus::ACTIVE,
                    'company_id'                => $request->user()->company_id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('purchase_transactions.stored'),
                'data'    => new PurchaseTransactionResource(
                    $transaction->load(['supplier', 'createdBy', 'details', 'details.product'])
                ),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(PosPurchaseTransaction $purchaseTransaction)
    {
        return response()->json([
            'success' => true,
            'message' => __('purchase_transactions.detail'),
            'data'    => new PurchaseTransactionResource(
                $purchaseTransaction->load(['supplier', 'createdBy', 'details', 'details.product'])
            ),
        ]);
    }

    public function cancel(PosPurchaseTransaction $purchaseTransaction)
    {
        if ($purchaseTransaction->transaction_status === PosTransactionStatus::CANCEL) {
            return response()->json([
                'success' => false,
                'message' => __('purchase_transactions.already_cancelled'),
                'code'    => 422,
            ], 422);
        }

        if ($purchaseTransaction->transaction_status !== PosTransactionStatus::PAID) {
            return response()->json([
                'success' => false,
                'message' => __('purchase_transactions.cannot_cancel'),
                'code'    => 422,
            ], 422);
        }

        DB::beginTransaction();

        try {
            $purchaseTransaction->update([
                'transaction_status' => PosTransactionStatus::CANCEL,
            ]);

            // Rollback stok per detail
            foreach ($purchaseTransaction->details as $detail) {
                $product     = $detail->product;
                $stockBefore = $product->stock;
                $stockAfter  = $stockBefore - $detail->quantity;

                $product->update(['stock' => max(0, $stockAfter)]);

                PosStockMutation::create([
                    'type'         => PosStockMutationType::ADJUST_OUT,
                    'quantity'     => $detail->quantity,
                    'stock_before' => $stockBefore,
                    'stock_after'  => max(0, $stockAfter),
                    'notes'        => "Pembatalan pembelian #{$purchaseTransaction->transaction_code}",
                    'product_id'   => $product->id,
                    'company_id'   => $purchaseTransaction->company_id,
                    'reference_id' => $detail->id,
                    'created_by'   => request()->user()->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('purchase_transactions.cancelled'),
                'data'    => new PurchaseTransactionResource(
                    $purchaseTransaction->load(['supplier', 'createdBy', 'details', 'details.product'])
                ),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
