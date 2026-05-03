<?php

namespace App\Models;

use App\Enums\PaymentType;
use App\Enums\TransactionStatus;
use App\Models\Scopes\CompanyScope;
use App\Traits\HasUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseTransaction extends Model
{
    use HasFactory, SoftDeletes, HasUlid;

    protected $fillable = [
        'ulid',
        'transaction_code',
        'transaction_date',
        'discount',
        'total',
        'paid',
        'payment_type',
        'transaction_status',
        'supplier_id',
        'created_by',
        'company_id',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'payment_type' => PaymentType::class,
        'transaction_status' => TransactionStatus::class
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }
}