<?php

namespace App\Exports;

use App\Models\Material;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MaterialsExport implements FromArray, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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
        $query = Material::query();

        // 🔍 SEARCH
        if ($this->request->filled('q')) {
            $query->where(function ($q) {
                $q->where('material_name', 'like', '%' . $this->request->q . '%')
                    ->orWhere('material_code', 'like', '%' . $this->request->q . '%');
            });
        }

        // 🗂 FILTER CATEGORY
        if ($this->request->filled('category')) {
            $query->where('category', $this->request->category);
        }

        return $query->orderBy('material_name')->get()->map(function ($m) {
            return [
                'material_code' => $m->material_code,
                'material_name' => $m->material_name,
                'category'      => $m->category,
                'unit'          => $m->unit,
                'stock'         => (int) ($m->stock ?? 0),
                'description'   => $m->description ?: '-',
            ];
        })->toArray();
    }
}
