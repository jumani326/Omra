<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PilgrimsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private $collection
    ) {}

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Prénom',
            'Nom',
            'Email',
            'Téléphone',
            'Passeport',
            'Nationalité',
            'Statut',
            'Branche',
            'Forfait',
            'Date inscription',
        ];
    }

    public function map($pilgrim): array
    {
        return [
            $pilgrim->id,
            $pilgrim->first_name,
            $pilgrim->last_name,
            $pilgrim->email ?? '',
            $pilgrim->phone ?? '',
            $pilgrim->passport_no,
            $pilgrim->nationality ?? '',
            ucfirst(str_replace('_', ' ', $pilgrim->status)),
            $pilgrim->branch?->name ?? '—',
            $pilgrim->package?->name ?? '—',
            $pilgrim->created_at?->format('d/m/Y H:i') ?? '',
        ];
    }
}
