<?php

namespace App\Exports;

use App\Models\DamagedMaterial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DamagedMaterialExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = DamagedMaterial::with('material','team','reporter');

        // SEARCH
        if ($this->request->filled('q')) {
            $q = strtolower($this->request->q);

            $query->where(function ($w) use ($q) {

                $w->whereHas('material', fn ($m) =>
                    $m->where('material_name', 'like', "%$q%")
                );

                if (str_contains($q, 'gudang')) {
                    $w->orWhere('source', 'warehouse');
                }

                $w->orWhereHas('team', fn ($t) =>
                    $t->where('team_name', 'like', "%$q%")
                );
            });
        }

        // DATE RANGE
        if ($this->request->filled('date_range')) {
            $cleaned = str_replace([' to ', ' - ', '–'], ',', $this->request->date_range);
            $dates = explode(',', $cleaned);

            if (count($dates) === 2) {
                $start = trim($dates[0]) . ' 00:00:00';
                $end   = trim($dates[1]) . ' 23:59:59';

                $query->whereBetween('reported_at', [$start, $end]);
            }
        }

        return $query->latest()->get()
            ->map(function ($row, $i) {
                return [
                    'No'        => $i + 1,
                    'Material'  => $row->material->material_name,
                    'Jumlah'    => $row->qty,
                    'Satuan'    => $row->material->unit,
                    'Sumber'    => $row->source === 'warehouse'
                                    ? 'Gudang'
                                    : ($row->team?->team_name ?? '-'),
                    'Penyebab'  => $row->cause,
                    'Pelapor'  => $row->reporter->name,
                    'Tanggal'  => $row->reported_at->format('Y-m-d H:i'),
                    'Catatan'  => $row->notes ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Material',
            'Jumlah',
            'Satuan',
            'Sumber',
            'Penyebab',
            'Pelapor',
            'Tanggal',
            'Catatan',
        ];
    }
}

