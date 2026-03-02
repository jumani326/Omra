<?php

namespace App\Console\Commands;

use App\Mail\VisaApprovedMail;
use App\Models\Notification;
use App\Models\Visa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class VisaSyncApprovedCommand extends Command
{
    protected $signature = 'visas:sync-approved {--send-mail=1 : Envoyer les emails aux pèlerins}';
    protected $description = "Applique la logique d'approbation (statut pèlerin, notifications, email avec PDF) à tous les visas déjà approuvés";

    public function handle(): int
    {
        $this->info('Synchronisation des visas approuvés...');

        $visas = Visa::with(['pilgrim.user', 'pilgrim.package'])
            ->where('status', 'approved')
            ->get();

        if ($visas->isEmpty()) {
            $this->info('Aucun visa avec statut "approved" trouvé.');
            return self::SUCCESS;
        }

        $sendMail = (bool) $this->option('send-mail');
        $countProcessed = 0;

        foreach ($visas as $visa) {
            $pilgrim = $visa->pilgrim;
            if (!$pilgrim) {
                continue;
            }

            // Mettre à jour le statut du pèlerin si besoin
            if ($pilgrim->status !== 'visa_approved') {
                $pilgrim->update(['status' => 'visa_approved']);
                $this->line("Statut pèlerin #{$pilgrim->id} mis à jour en 'visa_approved'.");
            }

            // Créer une notification côté client
            if ($pilgrim->user_id) {
                Notification::create([
                    'user_id' => $pilgrim->user_id,
                    'type' => 'visa',
                    'channel' => 'in_app',
                    'content' => sprintf(
                        'Votre visa pour le forfait "%s" a été approuvé. Vous pouvez maintenant consulter vos documents et procéder au paiement du solde.',
                        $pilgrim->package?->name ?? 'Omra'
                    ),
                    'sent_at' => now(),
                ]);
            }

            // Envoyer l'email avec PDF si demandé
            if ($sendMail) {
                $email = $pilgrim->email ?: $pilgrim->user?->email;
                if ($email) {
                    Mail::to($email)->send(new VisaApprovedMail($pilgrim, $visa));
                    $this->line("Email envoyé à {$email} pour le pèlerin #{$pilgrim->id}.");
                }
            }

            $countProcessed++;
        }

        $this->info("Synchronisation terminée. {$countProcessed} visa(s) traités.");
        return self::SUCCESS;
    }
}

