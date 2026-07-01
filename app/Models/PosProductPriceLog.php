<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosProductPriceLog extends Model
{
    protected $table = 'pos_product_price_logs';

    protected $fillable = [
        'product_id',
        'base_price_old',
        'base_price_new',
        'leader_price_old',
        'leader_price_new',
        'marketing_price_old',
        'marketing_price_new',
        'sell_price_old',
        'sell_price_new',
        'changed_by',
    ];

    protected $casts = [
        'base_price_old' => 'decimal:2',
        'base_price_new' => 'decimal:2',
        'leader_price_old' => 'decimal:2',
        'leader_price_new' => 'decimal:2',
        'marketing_price_old' => 'decimal:2',
        'marketing_price_new' => 'decimal:2',
        'sell_price_old' => 'decimal:2',
        'sell_price_new' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(PosProduct::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
