<?php

namespace App\Http\Controllers;

use App\Models\MaterialOut;
use App\Models\MaterialOutDetail;
use App\Models\Material;
use App\Models\TeamStock;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\MaterialOutDetailExport;
use Maatwebsite\Excel\Facades\Excel;


class MaterialOutController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 query dasar
        $query = MaterialOut::with('team', 'user');

        // 🔐 filter role teknisi
        if (Auth::user()->role === 'teknisi') {
            $query->where('team_id', Auth::user()->team_id);
        }

        // 🔍 search
        $query->when($request->q, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('team', function ($t) use ($request) {
                    $t->where('team_name', 'like', "%{$request->q}%");
                })
                    ->orWhereHas('user', function ($u) use ($request) {
                        $u->where('name', 'like', "%{$request->q}%");
                    });
            });
        });

        // 📅 filter tanggal
        $query->when($request->date_range, function ($query) use ($request) {

            $dates = explode(' to ', $request->date_range);

            $start = $dates[0];
            $end   = $dates[1] ?? $dates[0];

            $query->whereBetween('created_at', [
                $start . ' 00:00:00',
                $end   . ' 23:59:59',
            ]);
        });

        // 📦 hasil akhir
        $outs = $query
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('material_out.index', compact('outs'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new MaterialOutDetailExport($request),
            'distribusi-material.xlsx'
        );
    }

    public function create()
    {
        return view('material_out.create', [
            'teams' => Team::all(),
            // 🔥 kirim stok gudang dari materials.stock
            'materials' => Material::select(
                'id',
                'material_name',
                'stock'
            )->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:teams,id',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {

            // HEADER
            $out = MaterialOut::create([
                'team_id' => $request->team_id,
                'user_id' => Auth::id(),
                'notes'   => $request->notes,
            ]);

            foreach ($request->materials as $item) {

                $material = Material::lockForUpdate()->findOrFail($item['material_id']);

                // ❗ VALIDASI STOK GUDANG
                if ($item['quantity'] > $material->stock) {
                    throw new \Exception(
                        "Stok {$material->material_name} tidak mencukupi"
                    );
                }

                // 🔻 KURANGI STOK GUDANG
                $material->decrement('stock', $item['quantity']);

                // DETAIL DISTRIBUSI
                MaterialOutDetail::create([
                    'material_out_id' => $out->id,
                    'material_id'     => $material->id,
                    'quantity'        => $item['quantity'],
                ]);

                // 🔺 TAMBAH STOK TIM
                TeamStock::updateOrCreate(
                    [
                        'team_id'     => $request->team_id,
                        'material_id' => $material->id,
                    ],
                    [
                        'quantity'   => DB::raw("quantity + {$item['quantity']}"),
                        'updated_by' => Auth::id(),
                    ]
                );
            }
        });

        return redirect()->route('material-out.index')
            ->with('success', 'Distribusi berhasil dicatat.');
    }

    public function show(MaterialOut $materialOut)
    {
        $materialOut->load('details.material', 'team', 'user');
        return view('material_out.show', compact('materialOut'));
    }

    public function destroy(MaterialOut $materialOut)
    {
        DB::transaction(function () use ($materialOut) {

            foreach ($materialOut->details as $d) {

                // 🔺 BALIKIN KE GUDANG
                $d->material->increment('stock', $d->quantity);

                // 🔻 KURANGI STOK TIM
                TeamStock::where('team_id', $materialOut->team_id)
                    ->where('material_id', $d->material_id)
                    ->decrement('quantity', $d->quantity);
            }

            $materialOut->delete();
        });

        return redirect()->route('material-out.index')
            ->with('success', 'Distribusi berhasil dihapus.');
    }
}
