<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GroupPilgrimsListExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function __construct(
        private Collection $pilgrims,
        private string $groupName
    ) {}

    public function collection(): Collection
    {
        return $this->pilgrims;
    }

    public function headings(): array
    {
        return [
            'N°',
            'Prénom',
            'Nom',
            'Email',
            'Téléphone',
            'N° Passeport',
            'Nationalité',
            'Statut',
        ];
    }

    public function map($pilgrim): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $pilgrim->first_name ?? '',
            $pilgrim->last_name ?? '',
            $pilgrim->email ?? '—',
            $pilgrim->phone ?? '—',
            $pilgrim->passport_no ?? '—',
            $pilgrim->nationality ?? '—',
            $pilgrim->status ? ucfirst(str_replace('_', ' ', $pilgrim->status)) : '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F3F2E']]],
            'A' => ['alignment' => ['horizontal' => 'center']],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 18,
            'C' => 18,
            'D' => 28,
            'E' => 16,
            'F' => 18,
            'G' => 14,
            'H' => 16,
        ];
    }
}
