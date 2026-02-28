<?php

namespace App\Console\Commands;

use App\Models\Visa;
use Illuminate\Console\Command;

class VisaExpiringCommand extends Command
{
    protected $signature = 'visas:expiring {--days=30 : Jours avant expiration}';
    protected $description = 'Liste les visas qui expirent dans les X prochains jours (alerte J-30)';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $visas = Visa::with('pilgrim')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('expiry_date', '>=', now())
            ->get();

        if ($visas->isEmpty()) {
            $this->info("Aucun visa n'expire dans les {$days} prochains jours.");
            return self::SUCCESS;
        }

        $this->warn("{$visas->count()} visa(s) expire(nt) dans les {$days} jours :");
        foreach ($visas as $v) {
            $this->line("  - #{$v->id} Pèlerin: {$v->pilgrim->first_name} {$v->pilgrim->last_name} — Expire: {$v->expiry_date->format('d/m/Y')}");
        }
        return self::SUCCESS;
    }
}
