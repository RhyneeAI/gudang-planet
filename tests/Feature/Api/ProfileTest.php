<?php

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->user    = User::factory()->owner()->create([
        'username'   => 'testuser',
        'password'   => Hash::make('oldpassword'),
        'company_id' => $this->company->id,
    ]);
});

// =============================
// PROFILE SHOW
// =============================

it('can get own profile', function () {
    $this->actingAs($this->user)
        ->getJson('/api/v1/profile')
        ->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['uuid', 'name', 'username', 'email', 'role']
        ])
        ->assertJsonPath('data.username', 'testuser');
});

it('returns 401 when not authenticated on profile show', function () {
    $this->getJson('/api/v1/profile')->assertStatus(401);
});

// =============================
// PROFILE UPDATE
// =============================

it('can update profile', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/v1/profile', [
            'name'  => 'Updated Name',
            'phone' => '08123456789',
        ])
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.phone', '08123456789');
});

it('can partial update profile', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/v1/profile', ['name' => 'Only Name Changed'])
        ->assertStatus(200)
        ->assertJsonPath('data.name', 'Only Name Changed');
});

it('returns 422 when email is invalid format', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/v1/profile', ['email' => 'bukan-email'])
        ->assertStatus(422);
});

it('returns 422 when email is already taken', function () {
    User::factory()->create([
        'email'      => 'taken@example.com',
        'company_id' => $this->company->id,
    ]);

    $this->actingAs($this->user)
        ->patchJson('/api/v1/profile', ['email' => 'taken@example.com'])
        ->assertStatus(422);
});

it('can update email to own email without conflict', function () {
    $this->user->update(['email' => 'myown@example.com']);

    $this->actingAs($this->user)
        ->patchJson('/api/v1/profile', ['email' => 'myown@example.com'])
        ->assertStatus(200);
});

it('returns 401 when not authenticated on profile update', function () {
    $this->patchJson('/api/v1/profile', ['name' => 'Test'])
        ->assertStatus(401);
});