<?php

namespace App\Imports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return Material::updateOrCreate(
            [
                'material_code' => $row['material_code'],
            ],
            [
                'material_name' => $row['material_name'],
                'category'      => $row['category'],
                'unit'          => $row['unit'],
                'stock'         => $row['stock'] ?? 0,
                'description'   => $row['description'] ?? null,
            ]
        );
    }
}
