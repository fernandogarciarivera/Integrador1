<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use chillerlan\QRCode\{QRCode, QROptions};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerarQrPedidos extends Command
{
    protected $signature = 'pedidos:regenerar-qr
                            {--id= : Solo un pedido específico}
                            {--force : Regenera todos, incluso si la URL ya parece correcta}';
    protected $description = 'Regenera imagen_qr y el SVG del QR con la APP_URL actual';

    public function handle(): int
    {
        $query = Pedido::query();

        if ($id = $this->option('id')) {
            $query->whereKey($id);
        }

        $total = $query->count();
        if ($total === 0) {
            $this->warn('No hay pedidos para procesar.');
            return self::SUCCESS;
        }

        $this->info("Procesando {$total} pedido(s) con APP_URL=" . config('app.url'));

        $bar = $this->output->createProgressBar($total);
        $actualizados = 0;

        $query->chunkById(100, function ($pedidos) use ($bar, &$actualizados) {
            foreach ($pedidos as $pedido) {
                // El QR embebe la URL de seguimiento (que depende de APP_URL/LAN_HOST_IP).
                $urlSeguimiento = $this->urlPublica('mi-pedido/' . $pedido->seguimiento_token);
                // Reescribe el SVG del QR con la URL actual.
                $ruta = "qr/{$pedido->id}.svg";
                Storage::disk('public')->put(
                    $ruta,
                    (new QRCode(new QROptions(['outputBase64' => false])))->render($urlSeguimiento)
                );
                // Reasigna imagen_qr con la nueva URL base.
                //$pedido->update(['imagen_qr' => $this->urlPublica('storage/' . $ruta)]);
                $pedido->update(['imagen_qr' => 'storage/' . $ruta]);
                $actualizados++;
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("✓ {$actualizados} pedido(s) regenerados.");

        return self::SUCCESS;
    }

    // Duplicado perezoso de CajaController::urlPublica (método privado).
    private function urlPublica(string $path): string
    {
        $requestHost = request()->getSchemeAndHttpHost();
        $lanHost = config('app.lan_host_ip');
        $configUrl = config('app.url');

        if ($requestHost && !str_contains($requestHost, 'localhost') && !str_contains($requestHost, '127.0.0.1')) {
            return rtrim($requestHost, '/') . '/' . ltrim($path, '/');
        }

        if ($lanHost) {
            $port = config('app.lan_port');
            $suffix = $port ? ":{$port}" : '';
            return rtrim("http://{$lanHost}{$suffix}", '/') . '/' . ltrim($path, '/');
        }

        if ($configUrl && !str_contains($configUrl, 'localhost') && !str_contains($configUrl, '127.0.0.1')) {
            return rtrim($configUrl, '/') . '/' . ltrim($path, '/');
        }

        return 'http://127.0.0.1/' . ltrim($path, '/');
    }
}
