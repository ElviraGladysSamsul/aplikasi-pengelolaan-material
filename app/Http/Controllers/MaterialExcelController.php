<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\MaterialsExport;
use App\Exports\MaterialsTemplateExport;
use App\Imports\MaterialsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class MaterialExcelController extends Controller
{
    private function canImport()
    {
        return in_array(Auth::user()->role, [
            'superadmin',
            'admin',
            'supervisor',
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(
            new MaterialsExport($request),
            'materials.xlsx'
        );
    }

    public function template()
    {
        return Excel::download(
            new MaterialsTemplateExport,
            'materials_template.xlsx'
        );
    }

    public function import(Request $request)
    {
        if (!$this->canImport()) {
            abort(403, 'Tidak punya akses import');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new MaterialsImport, $request->file('file'));

        return back()->with('success', 'Import material berhasil!');
    }
}
