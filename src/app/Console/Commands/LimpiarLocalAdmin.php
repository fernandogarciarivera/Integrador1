<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarLocalAdmin extends Command
{
    protected $signature = 'trabajadores:limpiar-local-admin
                            {--dry-run : Muestra cuántos registros cambiaría sin modificar la BD}';

    protected $description = 'Coloca en NULL el local_id de los trabajadores con perfil ADMIN';

    public function handle(): int
    {
        $trabajadores = DB::table('trabajadores')
            ->where('rol', 'ADMIN')
            ->whereNotNull('local_id');

        if ($this->option('dry-run')) {
            $this->info("Trabajadores ADMIN que se actualizarían: {$trabajadores->count()}");

            return self::SUCCESS;
        }

        $actualizados = $trabajadores->update([
            'local_id' => null,
            'updated_at' => now(),
        ]);

        $this->info("Trabajadores ADMIN actualizados: {$actualizados}");

        return self::SUCCESS;
    }
}
