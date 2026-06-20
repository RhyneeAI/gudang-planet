<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubCompany extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'name',
        'code',
        'address',
        'latitude',
        'longitude',
        'radius_meter',
        'is_active',
        'mandor_id',
        'company_id',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius_meter' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function mandor()
    {
        return $this->belongsTo(User::class, 'mandor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function wallet()
    {
        return $this->hasOne(OpsWallet::class, 'sub_company_id');
    }

    public function incomes()
    {
        return $this->hasMany(OpsIncome::class, 'sub_company_id');
    }

    public function expenses()
    {
        return $this->hasMany(OpsExpense::class, 'sub_company_id');
    }

    public function employeeProfiles()
    {
        return $this->hasMany(AbsEmployeeProfile::class, 'sub_company_id');
    }

    public function attendances()
    {
        return $this->hasMany(AbsAttendance::class, 'sub_company_id');
    }

    public function hasGpsConfigured(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }
}
