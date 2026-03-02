<?php

namespace App\Console\Commands;

use App\Mail\TransactionValideeClientMail;
use App\Models\TransactionDigitale;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendValidationEmailsToOldTransactions extends Command
{
    protected $signature = 'transaction-digitales:send-validation-emails
                            {--dry-run : Afficher les transactions sans envoyer les emails}';

    protected $description = 'Envoie l\'email de validation (avec fiche PDF) aux clients pour les transactions déjà validées qui ne l\'ont pas encore reçu.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $transactions = TransactionDigitale::query()
            ->where('statut', 'valide')
            ->whereNull('validation_email_sent_at')
            ->with(['compteMarchand', 'pilgrim.user'])
            ->orderBy('id')
            ->get();

        if ($transactions->isEmpty()) {
            $this->info('Aucune transaction validée en attente d\'envoi d\'email.');
            return self::SUCCESS;
        }

        $this->info($transactions->count() . ' transaction(s) validée(s) sans email de validation envoyé.');

        if ($dryRun) {
            $this->warn('Mode dry-run : aucun email ne sera envoyé.');
            foreach ($transactions as $tx) {
                $email = $tx->pilgrim?->email ?? $tx->pilgrim?->user?->email ?? '—';
                $this->line("  #{$tx->id} — {$tx->client_display} — {$email} — " . number_format($tx->montant, 0, ',', ' ') . ' FDJ');
            }
            return self::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($transactions as $tx) {
            $clientEmail = $tx->pilgrim?->email ?? $tx->pilgrim?->user?->email;
            if (!$clientEmail) {
                $this->warn("  #{$tx->id} — Pas d'email client pour « {$tx->client_display} », ignoré.");
                $skipped++;
                continue;
            }
            try {
                Mail::to($clientEmail)->send(new TransactionValideeClientMail($tx));
                $tx->update(['validation_email_sent_at' => now()]);
                $this->info("  #{$tx->id} — Email envoyé à {$clientEmail}");
                $sent++;
            } catch (\Throwable $e) {
                $this->error("  #{$tx->id} — Erreur : " . $e->getMessage());
                report($e);
            }
        }

        $this->newLine();
        $this->info("Terminé : {$sent} email(s) envoyé(s), {$skipped} ignoré(s).");
        return self::SUCCESS;
    }
}
