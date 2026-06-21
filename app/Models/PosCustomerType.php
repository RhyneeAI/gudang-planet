<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosCustomerType extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $table = 'customer_types';

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    protected $fillable = [
        'uuid',
        'type',
        'discount',
        'created_by',
        'company_id',
    ];

    protected $casts = [
        'discount' => 'double',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customers()
    {
        return $this->hasMany(PosCustomer::class, 'customer_type_id');
    }
}
