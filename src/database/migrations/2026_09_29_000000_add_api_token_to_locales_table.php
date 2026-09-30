<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Hash, Schema};

return new class extends Migration {
    public function up(): void
    {
        Schema::table('locales', function (Blueprint $table) {
            $table->string('api_token', 64)->nullable()->unique()->after('codigo');
        });

        // Backfill: token + usuario POS para cada local existente.
        DB::table('locales')->whereNull('api_token')->orderBy('id')->chunkById(100, function ($locales) {
            foreach ($locales as $local) {
                DB::table('locales')->where('id', $local->id)->update([
                    'api_token' => \Illuminate\Support\Str::random(64),
                ]);

                $email = 'pos.' . $local->id . '@local.api';

                if (DB::table('users')->where('email', $email)->exists()) {
                    continue;
                }

                $userId = DB::table('users')->insertGetId([
                    'name'                 => 'POS  1 ' . $local->nombre,
                    'email'                => $email,
                    'password'             => Hash::make('12345678'),
                    'must_change_password' => false,
                    'email_verified_at'    => now(),
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                DB::table('trabajadores')->insert([
                    'user_id'        => $userId,
                    'restaurante_id' => $local->restaurante_id,
                    'local_id'       => $local->id,
                    'rol'            => 'POS',
                    'puesto'         => 'POS',
                    'activo'         => true,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('locales', fn(Blueprint $t) => $t->dropColumn('api_token'));
    }
};
