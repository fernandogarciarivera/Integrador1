<?php

namespace App\Console\Commands;

use App\Models\{Restaurante, Locale, User, Trabajador};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{DB, Hash};
use Illuminate\Support\Str;

class GenerarUsuariosPorLocal extends Command
{
    protected $signature = 'usuarios:generar-por-local
                            {--restaurante= : Solo un restaurante (id)}
                            {--local= : Solo un local (id)}
                            {--password=12345678 : Contraseña inicial}
                            {--dry-run : Solo simula, no escribe en BD}';

    protected $description = 'Crea usuarios+trabajadores (ADMIN, GERENTE, CAJA, COCINA, DESPACHO) por cada local';

    private const ROLES = ['ADMIN', 'GERENTE', 'CAJA', 'COCINA', 'DESPACHO'];

    public function handle(): int
    {
        $restauranteId = $this->option('restaurante');
        $localId = $this->option('local');
        $password = $this->option('password');
        $dryRun = $this->option('dry-run');

        $locales = Locale::query()
            ->with('restaurante')
            ->when($restauranteId, fn($q) => $q->where('restaurante_id', $restauranteId))
            ->when($localId, fn($q) => $q->whereKey($localId))
            ->where('estado', 'ACTIVO')
            ->orderBy('restaurante_id')
            ->orderBy('id')
            ->get();

        if ($locales->isEmpty()) {
            $this->warn('No hay locales que cumplan el filtro.');
            return self::SUCCESS;
        }

        $this->info("Procesando {$locales->count()} local(es)...");
        if ($dryRun) {
            $this->warn('[DRY-RUN] No se escribirá en BD.');
        }

        $bar = $this->output->createProgressBar($locales->count() * count(self::ROLES));
        $creados = 0;
        $existentes = 0;

        foreach ($locales as $local) {
            foreach (self::ROLES as $rol) {
                $email = $this->emailPara($rol, $local);

                $existe = User::where('email', $email)->exists()
                    || Trabajador::where('local_id', $local->id)
                    ->where('rol', $rol)
                    ->exists();

                if ($existe) {
                    $existentes++;
                    $bar->advance();
                    continue;
                }

                if (! $dryRun) {
                    DB::transaction(function () use ($local, $rol, $email, $password) {
                        $user = User::create([
                            'name'                 => "{$rol} " . $local->nombre,
                            'email'                => $email,
                            'password'             => Hash::make($password),
                            'must_change_password' => false,
                            'email_verified_at'    => now(),
                        ]);

                        Trabajador::create([
                            'user_id'        => $user->id,
                            'restaurante_id' => $local->restaurante_id,
                            'local_id'       => $local->id,
                            'rol'            => $rol,
                            'puesto'         => $rol,
                            'activo'         => true,
                        ]);
                    });
                }

                $creados++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Creados: {$creados}");
        $this->line("  Ya existentes (omitidos): {$existentes}");
        $this->line("  Contraseña inicial: {$password}");

        return self::SUCCESS;
    }

    /** Email estable y legible: {rol}-{slug_rest}-{slug_local}@prueba.com */
    private function emailPara(string $rol, Locale $local): string
    {
        $rest = Str::slug($local->restaurante?->nombre ?? 'rest');
        $loc = Str::slug($local->nombre);

        return strtolower("{$rol}-{$rest}-{$loc}@prueba.com");
    }
}
