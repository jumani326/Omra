<?php

namespace App\Mail;

use App\Models\TransactionDigitale;
use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TransactionValideeClientMail extends Mailable
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
            subject: 'Votre paiement a été validé par l\'agence — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.transaction-validee-client',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->transaction->pdf_path && Storage::disk('public')->exists($this->transaction->pdf_path)) {
            $attachments[] = Attachment::fromStorageDisk('public', $this->transaction->pdf_path)
                ->as('fiche-paiement-valide-' . $this->transaction->id . '.pdf');
            return $attachments;
        }

        // Régénérer le PDF si absent
        $html = view('pdf.transaction-digitale-fiche', ['transaction' => $this->transaction])->render();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $attachments[] = Attachment::fromData(
            fn () => $dompdf->output(),
            'fiche-paiement-valide-' . $this->transaction->id . '.pdf'
        )->withMime('application/pdf');

        return $attachments;
    }
}
