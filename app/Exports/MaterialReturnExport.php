<?php

namespace App\Exports;

use App\Models\MaterialReturn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaterialReturnExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = MaterialReturn::with('material','team','admin');

        // SEARCH
        if ($this->request->filled('q')) {
            $q = $this->request->q;
            $query->whereHas('material', fn ($m) =>
                $m->where('material_name', 'like', "%$q%")
            )->orWhereHas('team', fn ($t) =>
                $t->where('team_name', 'like', "%$q%")
            );
        }

        // DATE RANGE
        if ($this->request->filled('date_range')) {
            $cleaned = str_replace([' to ', ' - ', '–'], ',', $this->request->date_range);
            $dates = explode(',', $cleaned);

            if (count($dates) === 2) {
                $start = trim($dates[0]) . ' 00:00:00';
                $end   = trim($dates[1]) . ' 23:59:59';
                $query->whereBetween('returned_at', [$start, $end]);
            }
        }

        return $query->latest()->get()
            ->map(function ($row, $i) {
                return [
                    'No'        => $i + 1,
                    'Tim'       => $row->team->team_name,
                    'Material'  => $row->material->material_name,
                    'Jumlah'    => $row->qty,
                    'Satuan'    => $row->material->unit,
                    'Penerima'  => $row->admin->name,
                    'Tanggal'   => $row->returned_at->format('Y-m-d H:i'),
                    'Catatan'   => $row->notes ?? '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tim',
            'Material',
            'Jumlah',
            'Satuan',
            'Penerima',
            'Tanggal',
            'Catatan',
        ];
    }
}

