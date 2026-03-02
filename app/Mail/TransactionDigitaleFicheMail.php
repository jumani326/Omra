<?php

namespace App\Mail;

use App\Models\TransactionDigitale;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TransactionDigitaleFicheMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TransactionDigitale $transaction
    ) {
        $this->transaction->load(['compteMarchand', 'pilgrim']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Fiche de paiement client — ' . ($this->transaction->client_nom ?? 'Transaction #' . $this->transaction->id),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-digitale-fiche',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        if ($this->transaction->pdf_path && Storage::disk('public')->exists($this->transaction->pdf_path)) {
            $attachments[] = Attachment::fromStorageDisk('public', $this->transaction->pdf_path)
                ->as('fiche-paiement-' . $this->transaction->id . '.pdf');
        }
        return $attachments;
    }
}
