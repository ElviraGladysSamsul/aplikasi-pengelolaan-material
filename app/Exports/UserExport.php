<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UserExport implements FromCollection, WithHeadings
{
    protected bool $template;
    protected ?Request $request;

    public function __construct(bool $template = false, Request $request = null)
    {
        $this->template = $template;
        $this->request  = $request;
    }

    public function collection()
    {
        // 📄 TEMPLATE
        if ($this->template) {
            return collect([
                [
                    'name'     => '',
                    'email'    => '',
                    'password' => '',
                    'role'     => '',
                    'team_id'  => '',
                ]
            ]);
        }

        // 📤 EXPORT DATA
        $query = User::with('team')->latest();

        if ($this->request?->filled('q')) {
            $q = $this->request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%");
            });
        }

        if ($this->request?->filled('role')) {
            $query->where('role', $this->request->role);
        }

        return $query->get()->map(function ($u) {
            return [
                $u->name,
                $u->email,
                '',                 // password dikosongkan
                $u->role,
                $u->team_id ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'password',
            'role',
            'team_id',
        ];
    }
}
