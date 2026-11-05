<?php

namespace App\Imports;

use App\Models\Team;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeamsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (!isset($row['nama_team'])) {
            return null;
        }

        return Team::create([
            'team_name'  => $row['nama_team'],
            'mitra'      => $row['mitra'] ?? null,
            'description' => $row['deskripsi'] ?? null,
        ]);
    }
}
