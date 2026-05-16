<?php

namespace App\Models;

use App\Enums\InstallmentStatus;
use App\Models\Scopes\CompanyScope;
use App\Traits\HasUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInstallmentPlan extends Model
{
    use HasFactory, SoftDeletes, HasUlid;

    protected $fillable = [
        'ulid',
        'purchase_transaction_id',
        'supplier_id',
        'total_amount',
        'paid_amount',
        'start_date',
        'status',
        'company_id',
    ];

    protected $casts = [
        'status'       => InstallmentStatus::class,
        'start_date'   => 'date',
        'total_amount' => 'float',
        'paid_amount'  => 'float',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    public function purchaseTransaction()
    {
        return $this->belongsTo(PurchaseTransaction::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchaseInstallmentPayment::class);
    }

    public function remainingAmount(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function isLastPayment(float $amount): bool
    {
        return ($this->paid_amount + $amount) >= $this->total_amount;
    }
}