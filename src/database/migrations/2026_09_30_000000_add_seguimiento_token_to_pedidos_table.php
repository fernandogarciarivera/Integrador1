<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('seguimiento_token', 64)->nullable()->unique()->after('codigo_qr');
        });

        // Backfill para pedidos existentes (no reescribe código)
        DB::table('pedidos')->whereNull('seguimiento_token')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                DB::table('pedidos')->where('id', $row->id)->update([
                    'seguimiento_token' => Str::random(40),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', fn(Blueprint $t) => $t->dropColumn('seguimiento_token'));
    }
};
