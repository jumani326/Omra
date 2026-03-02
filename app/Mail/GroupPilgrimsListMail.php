<?php

namespace App\Mail;

use App\Models\Group;
use Dompdf\Dompdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GroupPilgrimsListMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Group $group
    ) {
        $this->group->load('pilgrims');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Liste des pèlerins – ' . $this->group->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.group-pilgrims-list',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->group->name);
        $date = now()->format('Y-m-d');

        if ($this->group->pilgrims->isNotEmpty()) {
            // PDF bien présenté
            $dompdf = new Dompdf();
            $dompdf->loadHtml(view('pdf.group-pilgrims-list', ['group' => $this->group])->render());
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $attachments[] = Attachment::fromData(
                fn () => $dompdf->output(),
                "liste-pelerins-{$safeName}-{$date}.pdf"
            )->withMime('application/pdf');

            // Fichier Excel .xlsx stylisé (comme le PDF : en-têtes verts, colonnes, bordures)
            $attachments[] = Attachment::fromData(
                fn () => $this->buildXlsxContent(),
                "liste-pelerins-{$safeName}-{$date}.xlsx"
            )->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        }

        return $attachments;
    }

    protected function buildXlsxContent(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Liste pèlerins');

        // En-têtes (style comme le PDF : fond vert #0F3F2E, texte blanc gras)
        $headers = ['N°', 'Prénom', 'Nom', 'Email', 'Téléphone', 'N° Passeport', 'Nationalité', 'Statut'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $col++;
        }
        $headerRange = 'A1:H1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '0F3F2E'],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '333333']],
            ],
        ]);

        // Données
        $row = 2;
        foreach ($this->group->pilgrims as $i => $p) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $p->first_name ?? '');
            $sheet->setCellValue('C' . $row, $p->last_name ?? '');
            $sheet->setCellValue('D' . $row, $p->email ?? '—');
            $sheet->setCellValue('E' . $row, $p->phone ?? '—');
            $sheet->setCellValue('F' . $row, $p->passport_no ?? '—');
            $sheet->setCellValue('G' . $row, $p->nationality ?? '—');
            $sheet->setCellValue('H' . $row, $p->status ? ucfirst(str_replace('_', ' ', $p->status)) : '—');
            $row++;
        }

        // Bordures et largeurs de colonnes (comme le PDF)
        $dataRange = 'A1:H' . ($row - 1);
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(28);
        $sheet->getColumnDimension('E')->setWidth(16);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(16);

        // Lignes alternées (lisibilité, comme le PDF)
        for ($r = 2; $r < $row; $r++) {
            if (($r % 2) === 0) {
                $sheet->getStyle('A' . $r . ':H' . $r)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5'],
                    ],
                ]);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
        $writer->save($tempFile);
        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        return $content;
    }
}
