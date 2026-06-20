<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsEmployeeProfile extends Model
{
    use HasFactory;

    protected $table = 'abs_employee_profiles';

    protected $fillable = [
        'user_id',
        'abs_branch_id',
        'abs_shift_id',
        'daily_rate',
        'company_id',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(AbsBranch::class, 'abs_branch_id');
    }

    public function shift()
    {
        return $this->belongsTo(AbsShift::class, 'abs_shift_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
