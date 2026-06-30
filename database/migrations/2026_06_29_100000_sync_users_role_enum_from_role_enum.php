<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('role', ['MANAJER_GUDANG', 'MANAGER_GUDANG'])
            ->update(['role' => Role::GUDANG->value]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', Role::GUDANG->value)
            ->update(['role' => 'MANAGER_GUDANG']);
    }
};
