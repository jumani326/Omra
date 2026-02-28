<?php

namespace App\Console\Commands;

use App\Models\Pilgrim;
use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PaymentReminderCommand extends Command
{
    protected $signature = 'payments:reminder {--days=15 : Jours avant départ pour alerte solde}';
    protected $description = 'Alerte solde impayé (pèlerins avec départ dans J-X dont solde > 0)';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $departureLimit = now()->addDays($days);

        $pilgrims = Pilgrim::with('package')
            ->whereHas('package', fn ($q) => $q->where('departure_date', '<=', $departureLimit)->where('departure_date', '>=', now()))
            ->get()
            ->filter(function (Pilgrim $p) {
                $totalPaid = (float) $p->payments()->where('status', 'completed')->sum('amount');
                $price = (float) ($p->package->price ?? 0);
                return $price > 0 && $totalPaid < $price;
            });

        if ($pilgrims->isEmpty()) {
            $this->info("Aucun pèlerin avec solde impayé (départ dans les {$days} jours).");
            return self::SUCCESS;
        }

        $this->warn("{$pilgrims->count()} pèlerin(s) avec solde restant (départ dans les {$days} jours) :");
        foreach ($pilgrims as $p) {
            $paid = (float) $p->payments()->where('status', 'completed')->sum('amount');
            $total = (float) $p->package->price;
            $this->line("  - {$p->first_name} {$p->last_name} — Départ: {$p->package->departure_date->format('d/m/Y')} — Reste: " . number_format($total - $paid, 0) . " MAD");
        }
        return self::SUCCESS;
    }
}
