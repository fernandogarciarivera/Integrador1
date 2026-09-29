<?php

namespace App\Console\Commands;

use App\Models\Locale;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerarTokenLocal extends Command
{
    protected $signature = 'local:token {local_id}';
    protected $description = 'Genera/regenera el token API de un local';

    public function handle(): int
    {
        $local = Locale::findOrFail($this->argument('local_id'));
        $local->update(['api_token' => Str::random(64)]);
        $this->info("Token para {$local->nombre}: {$local->api_token}");
        return self::SUCCESS;
    }
}
