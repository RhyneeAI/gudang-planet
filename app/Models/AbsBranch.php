<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsBranch extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $table = 'abs_branches';

    protected $fillable = [
        'uuid',
        'name',
        'address',
        'latitude',
        'longitude',
        'radius_meter',
        'company_id',
    ];

    protected $casts = [
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

    public function employeeProfiles()
    {
        return $this->hasMany(AbsEmployeeProfile::class, 'abs_branch_id');
    }

    public function attendances()
    {
        return $this->hasMany(AbsAttendance::class, 'abs_branch_id');
    }
}
