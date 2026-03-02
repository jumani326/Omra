<?php

namespace App\Mail;

use App\Models\Pilgrim;
use App\Models\Visa;
use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class VisaApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pilgrim $pilgrim,
        public Visa $visa
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🛂 Votre visa Omra est approuvé',
            from: new Address(config('mail.from.address'), config('mail.from.name')),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.visa-approved',
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ((array) ($this->visa->documents ?? []) as $path) {
            if (is_string($path)) {
                $attachments[] = Attachment::fromStorageDisk('public', $path)
                    ->as(basename($path));
            }
        }

        // Générer un récapitulatif PDF du visa à partir d'une vue
        $dompdf = new Dompdf();
        $html = view('pdf.visa', ['visa' => $this->visa])->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'visa-' . ($this->visa->reference_no ?: $this->visa->id) . '.pdf';

        $attachments[] = Attachment::fromData(
            fn () => $dompdf->output(),
            $fileName
        )->withMime('application/pdf');

        return $attachments;
    }
}

