<?php

namespace Database\Factories;

use App\Models\AbsPayrollPeriod;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsDeductionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'abs_payroll_period_id' => AbsPayrollPeriod::factory(),
            'user_id' => fn (array $attrs) => AbsPayrollPeriod::find($attrs['abs_payroll_period_id'])?->user_id ?? User::factory()->karyawan(),
            'abs_attendance_id' => null,
            'reason' => fake()->randomElement(['Terlambat', 'Tidak Hadir', 'Izin', 'Sakit']),
            'amount' => fake()->randomFloat(2, 10000, 100000),
            'created_by' => User::factory()->admin(),
            'company_id' => fn (array $attrs) => AbsPayrollPeriod::find($attrs['abs_payroll_period_id'])?->company_id ?? Company::factory(),
        ];
    }
}
