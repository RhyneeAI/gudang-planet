<?php

use App\Models\AbsBranch;
use App\Models\AbsEmployeeProfile;
use App\Models\AbsShift;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    $this->company = Company::factory()->create();
    $this->admin = User::factory()->admin()->create([
        'company_id' => $this->company->id,
        'is_active' => true,
    ]);

    $this->branch = AbsBranch::create([
        'name' => 'Cabang Pusat',
        'address' => 'Jl. Test',
        'latitude' => -6.200000,
        'longitude' => 106.816666,
        'radius_meter' => 500,
        'company_id' => $this->company->id,
    ]);

    $this->shift = AbsShift::create([
        'name' => 'Shift Pagi',
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'company_id' => $this->company->id,
    ]);

    $this->employee = User::factory()->karyawan()->create([
        'company_id' => $this->company->id,
        'is_active' => true,
    ]);

    AbsEmployeeProfile::create([
        'user_id' => $this->employee->id,
        'abs_branch_id' => $this->branch->id,
        'abs_shift_id' => $this->shift->id,
        'daily_rate' => 100000,
        'company_id' => $this->company->id,
    ]);
});

it('admin can create branch and employee', function () {
    $this->actingAs($this->admin)
        ->postJson('/api/v1/abs/branches', [
            'name' => 'Cabang Baru',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ])
        ->assertStatus(201);

    $this->actingAs($this->admin)
        ->postJson('/api/v1/abs/employees', [
            'name' => 'Budi',
            'phone' => '081234567890',
            'password' => 'password123',
            'branch_uuid' => $this->branch->uuid,
            'shift_uuid' => $this->shift->uuid,
            'daily_rate' => 120000,
        ])
        ->assertStatus(201);
});

it('employee can check in within branch radius', function () {
    $photo = UploadedFile::fake()->image('selfie.jpg');

    $this->actingAs($this->employee)
        ->postJson('/api/v1/abs/me/attendance/check-in', [
            'photo' => $photo,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ])
        ->assertStatus(201)
        ->assertJsonPath('success', true);
});

it('employee check in is blocked outside branch radius', function () {
    $photo = UploadedFile::fake()->image('selfie.jpg');

    $this->actingAs($this->employee)
        ->postJson('/api/v1/abs/me/attendance/check-in', [
            'photo' => $photo,
            'latitude' => -6.3,
            'longitude' => 106.9,
        ])
        ->assertStatus(422)
        ->assertJsonPath('success', false);
});

it('owner can view dashboard but cannot create branch', function () {
    $owner = User::factory()->owner()->create([
        'company_id' => $this->company->id,
    ]);

    $this->actingAs($owner)
        ->getJson('/api/v1/abs/dashboard')
        ->assertStatus(200);

    $this->actingAs($owner)
        ->postJson('/api/v1/abs/branches', [
            'name' => 'Cabang Owner',
            'latitude' => -6.2,
            'longitude' => 106.8,
        ])
        ->assertStatus(403);
});
