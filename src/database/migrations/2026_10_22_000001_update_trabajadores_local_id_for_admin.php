<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $userId = DB::table('users')
            ->where('name', 'Super Admin')
            ->value('id');

        if ($userId !== null) {
            $tieneTrabajador = DB::table('trabajadores')
                ->where('user_id', $userId)
                ->exists();

            if (! $tieneTrabajador) {
                Schema::table('trabajadores', function (Blueprint $table) {
                    $table->unsignedBigInteger('restaurante_id')->nullable()->change();
                    $table->unsignedBigInteger('local_id')->nullable()->change();
                });
            }
        }

        DB::statement('SET SQL_SAFE_UPDATES = 0');

        DB::table('trabajadores')
            ->where('rol', 'ADMIN')
            ->update(['local_id' => null]);

        $userId = DB::table('users')
            ->where('name', 'Super Admin')
            ->value('id');

        if ($userId !== null) {
            $tieneTrabajador = DB::table('trabajadores')
                ->where('user_id', $userId)
                ->exists();

            if (! $tieneTrabajador) {
                DB::table('trabajadores')->insert([
                    'user_id' => $userId,
                    'restaurante_id' => null,
                    'local_id' => null,
                    'rol' => 'SUPER_ADMIN',
                    'puesto' => 'Super Admin',
                    'telefono' => null,
                    'activo' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        DB::statement('SET SQL_SAFE_UPDATES = 1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $userId = DB::table('users')
            ->where('name', 'Super Admin')
            ->value('id');

        if ($userId !== null) {
            DB::table('trabajadores')
                ->where('user_id', $userId)
                ->where('rol', 'SUPER_ADMIN')
                ->delete();
        }
    }
};
