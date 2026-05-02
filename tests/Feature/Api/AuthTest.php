<?php

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

// uses(RefreshDatabase::class);
// uses(TestCase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->user    = User::factory()->create([
        'username'   => 'testuser',
        'password'   => bcrypt('password123'),
        'company_id' => $this->company->id,
    ]);
});

// =============================
// LOGIN
// =============================

it('can login with valid credentials', function () {
    $response = $this->postJson('/api/v1/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'user',
                'token',
                'token_type',
            ]
        ])
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.token_type', 'Bearer');
});

it('returns 422 with invalid credentials', function () {
    $response = $this->postJson('/api/v1/login', [
        'username' => 'testuser',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false)
        ->assertJsonStructure([
            'errors' => ['username']
        ]);
});

it('returns 422 when username not found', function () {
    $response = $this->postJson('/api/v1/login', [
        'username' => 'tidakada',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('success', false);
});

it('returns 422 when username is empty', function () {
    $response = $this->postJson('/api/v1/login', [
        'username' => '',
        'password' => 'password123',
    ]);

    $response->assertStatus(422);
});

it('returns 422 when password is empty', function () {
    $response = $this->postJson('/api/v1/login', [
        'username' => 'testuser',
        'password' => '',
    ]);

    $response->assertStatus(422);
});

it('returns 422 when both fields are empty', function () {
    $response = $this->postJson('/api/v1/login', []);

    $response->assertStatus(422);
});

it('replaces existing token for same device on login', function () {
    // Login pertama
    $this->postJson('/api/v1/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ], ['User-Agent' => 'TestDevice']);

    // Login kedua dengan device yang sama
    $this->postJson('/api/v1/login', [
        'username' => 'testuser',
        'password' => 'password123',
    ], ['User-Agent' => 'TestDevice']);

    // Harus tetap hanya 1 token untuk device ini
    expect($this->user->tokens()->where('name', 'TestDevice')->count())->toBe(1);
});

// =============================
// LOGOUT
// =============================

it('can logout when authenticated', function () {
    $token = $this->user->createToken('TestDevice')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/v1/logout');

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    // Token harus sudah terhapus
    expect($this->user->tokens()->count())->toBe(0);
});

it('returns 401 when logout without token', function () {
    $response = $this->postJson('/api/v1/logout');

    $response->assertStatus(401);
});