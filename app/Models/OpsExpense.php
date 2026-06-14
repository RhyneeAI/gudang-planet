<?php

namespace App\Models;

use App\Enums\OpsExpenseType;
use App\Models\Scopes\CompanyScope;
use App\Traits\HasUlid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpsExpense extends Model
{
    use HasFactory, SoftDeletes, HasUlid;

    protected $fillable = [
        'ulid',
        'name',
        'amount',
        'date',
        'proof_file',
        'note',
        'expense_type',
        'mandor_id',
        'created_by',
        'company_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'expense_type' => OpsExpenseType::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new CompanyScope());
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mandor()
    {
        return $this->belongsTo(User::class, 'mandor_id');
    }

    public function transferConfirmation()
    {
        return $this->morphOne(OpsTransferConfirmation::class, 'confirmable');
    }
}
