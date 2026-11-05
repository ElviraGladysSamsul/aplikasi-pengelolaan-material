<?php

namespace App\Exports;

use App\Models\MaterialUsage;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialUsageExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return MaterialUsage::with([
            'team',
            'technician',
            'items.material'
        ])
            ->when($this->request->q, function ($q) {
                $q->where('no_wo', 'like', "%{$this->request->q}%")
                    ->orWhere('mitra', 'like', "%{$this->request->q}%")
                    ->orWhereHas('technician', function ($t) {
                        $t->where('name', 'like', "%{$this->request->q}%");
                    });
            })
            ->when($this->request->date_range, function ($q) {
                $dates = explode(' to ', $this->request->date_range);
                $start = $dates[0];
                $end   = $dates[1] ?? $dates[0];

                $q->whereBetween('created_at', [
                    $start . ' 00:00:00',
                    $end . ' 23:59:59'
                ]);
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No WO',
            'Mitra',
            'Jenis PSB',
            'Team',
            'Teknisi',
            'Material',
            'Qty',
        ];
    }

    public function map($usage): array
    {
        // 🔥 flatten material items
        $rows = [];

        foreach ($usage->items as $item) {
            $rows[] = [
                $usage->created_at->format('Y-m-d H:i'),
                $usage->no_wo,
                $usage->mitra,
                $usage->jenis_psb,
                optional($usage->team)->team_name,
                optional($usage->technician)->name,
                $item->material->material_name,
                $item->qty,
            ];
        }

        return $rows;
    }
}
