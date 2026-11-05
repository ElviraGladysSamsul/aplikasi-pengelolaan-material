<?php

namespace App\Exports;

use App\Models\MaterialIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MaterialInExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = MaterialIn::with('material', 'user');

        // SEARCH
        if ($this->request->filled('q')) {
            $query->whereHas('material', function ($q) {
                $q->where('material_name', 'like', '%' . $this->request->q . '%');
            });
        }

        // DATE RANGE
        if ($this->request->filled('date_range')) {
            $cleaned = str_replace([' to ', ' - ', '–'], ',', $this->request->date_range);
            $dates = explode(',', $cleaned);

            if (count($dates) === 2) {
                $start = trim($dates[0]) . ' 00:00:00';
                $end   = trim($dates[1]) . ' 23:59:59';
                $query->whereBetween('received_at', [$start, $end]);
            }
        }

        return $query->orderBy('received_at', 'desc')
            ->get()
            ->map(function ($row, $i) {
                return [
                    'No'        => $i + 1,
                    'Material'  => $row->material->material_name,
                    'Jumlah'    => $row->qty,
                    'Satuan'    => $row->material->unit,
                    'Asal'      => $row->source,
                    'Invoice'   => $row->invoice_number ?? '-',
                    'Penerima'  => $row->user->name,
                    'Tanggal'   => $row->received_at->format('Y-m-d H:i'),
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
            'Asal',
            'Invoice',
            'Penerima',
            'Tanggal',
        ];
    }
}
