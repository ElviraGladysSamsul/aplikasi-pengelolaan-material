<?php

namespace App\Http\Controllers;

use App\Exports\TeamsExport;
use App\Imports\TeamsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TeamExcelController extends Controller
{
    public function export(Request $request)
    {
        return Excel::download(new TeamsExport($request), 'teams.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new TeamsImport, $request->file('file'));

        return back()->with('success', 'Data team berhasil diimport.');
    }

    public function template()
    {
        return Excel::download(new TeamsExport(null, true), 'template_team.xlsx');
    }
}
