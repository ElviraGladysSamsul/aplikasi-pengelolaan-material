<?php

namespace App\Exports;

use App\Models\MaterialOutDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaterialOutDetailExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = MaterialOutDetail::with([
            'material',
            'materialOut.team',
            'materialOut.user'
        ]);

        /* ===============================
         | SEARCH (team / user / material)
         =============================== */
        if ($this->request->filled('q')) {
            $q = $this->request->q;

            $query->where(function ($sub) use ($q) {
                $sub->whereHas(
                    'material',
                    fn($m) =>
                    $m->where('material_name', 'like', "%{$q}%")
                )
                    ->orWhereHas(
                        'materialOut.team',
                        fn($t) =>
                        $t->where('team_name', 'like', "%{$q}%")
                    )
                    ->orWhereHas(
                        'materialOut.user',
                        fn($u) =>
                        $u->where('name', 'like', "%{$q}%")
                    );
            });
        }

        /* ===============================
         | DATE RANGE (HEADER DATE)
         =============================== */
        if ($this->request->filled('date_range')) {
            $cleaned = str_replace([' to ', ' - ', '–'], ',', $this->request->date_range);
            $dates = explode(',', $cleaned);

            if (count($dates) === 2) {
                $start = trim($dates[0]) . ' 00:00:00';
                $end   = trim($dates[1]) . ' 23:59:59';

                $query->whereHas(
                    'materialOut',
                    fn($o) =>
                    $o->whereBetween('created_at', [$start, $end])
                );
            }
        }

        return $query->latest()->get()->map(function ($row, $i) {
            return [
                'No'       => $i + 1,
                'Tim'      => $row->materialOut->team->team_name,
                'Admin'    => $row->materialOut->user->name,
                'Material' => $row->material->material_name,
                'Jumlah'   => $row->quantity,
                'Tanggal'  => $row->materialOut->created_at->format('Y-m-d H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tim',
            'Admin',
            'Material',
            'Jumlah',
            'Tanggal',
        ];
    }
}
