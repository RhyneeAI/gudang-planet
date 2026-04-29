<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerType extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    protected $fillable = [
        'uuid',
        'type',
        'discount',
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
}