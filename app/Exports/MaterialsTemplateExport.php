<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MaterialsTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'material_code',
            'material_name',
            'category',
            'unit',
            'stock',
            'description',
        ];
    }

    public function array(): array
    {
        return [
            [
                'MAT-001',
                'Contoh Material',
                'Kategori',
                'Unit',
                0,
                '-',
            ],
        ];
    }
}
