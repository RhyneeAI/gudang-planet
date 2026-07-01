<?php

namespace App\Observers;

use App\Models\PosProduct;
use App\Models\PosProductPriceLog;

class PosProductObserver
{
    public function saving(PosProduct $product): void
    {
        if (!$product->exists) {
            return;
        }

        $dirty = $product->getDirty();
        $priceFields = ['base_price', 'leader_price', 'marketing_price', 'sell_price'];

        $changes = [];
        foreach ($priceFields as $field) {
            if (array_key_exists($field, $dirty)) {
                $changes["{$field}_old"] = $product->getOriginal($field) ?? 0;
                $changes["{$field}_new"] = $product->$field ?? 0;
            }
        }

        if (empty($changes)) {
            return;
        }

        PosProductPriceLog::create(array_merge($changes, [
            'product_id' => $product->id,
            'changed_by' => auth()->id(),
        ]));
    }
}
