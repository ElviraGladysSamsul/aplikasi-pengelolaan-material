<?php

namespace App\Exports;

use App\Models\Team;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamsExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $template;

    public function __construct(Request $request = null, $template = false)
    {
        $this->request  = $request;
        $this->template = $template;
    }

    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }

        $query = Team::query();

        if ($this->request?->q) {
            $query->where('team_name', 'like', '%' . $this->request->q . '%')
                ->orWhere('mitra', 'like', '%' . $this->request->q . '%');
        }

        if ($this->request?->mitra) {
            $query->where('mitra', $this->request->mitra);
        }

        return $query->select(
            'team_name',
            'mitra',
            'description'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Nama Team',
            'Mitra',
            'Deskripsi',
        ];
    }
}
