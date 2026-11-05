<?php

namespace App\Http\Controllers;

use App\Models\DamagedMaterial;
use App\Models\Material;
use App\Models\TeamStock;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\DamagedMaterialExport;
use Maatwebsite\Excel\Facades\Excel;

class DamagedMaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = DamagedMaterial::with(['material', 'team', 'reporter'])
            ->latest();

        if (Auth::user()->role === 'teknisi') {
            $query->where('team_id', Auth::user()->team_id);
        }
        // 🔍 SEARCH (material | sumber)
        if ($request->filled('q')) {
            $q = strtolower($request->q);

            $query->where(function ($w) use ($q) {

                // ✅ cari nama material
                $w->whereHas('material', function ($m) use ($q) {
                    $m->where('material_name', 'like', "%{$q}%");
                });

                // ✅ cari "gudang"
                if (str_contains($q, 'gudang')) {
                    $w->orWhere('source', 'warehouse');
                }

                // ✅ cari nama tim (hanya yg source = team)
                $w->orWhereHas('team', function ($t) use ($q) {
                    $t->where('team_name', 'like', "%{$q}%");
                });
            });
        }

        // 📅 FILTER DATE RANGE
        if ($request->filled('date_range')) {

            $cleaned = str_replace([' to ', ' - ', '–'], ',', $request->date_range);
            $dates = explode(',', $cleaned);

            if (count($dates) === 2) {
                $start = trim($dates[0]) . ' 00:00:00';
                $end   = trim($dates[1]) . ' 23:59:59';

                $query->whereBetween('reported_at', [$start, $end]);
            }
        }

        // ✅ BARU paginate di AKHIR
        $data = $query->paginate(5)->withQueryString();

        return view('damaged_material.index', compact('data'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new DamagedMaterialExport($request),
            'material-rusak.xlsx'
        );
    }
    public function create()
    {
        return view('damaged_material.create', [
            'materials' => Material::select('id', 'material_name', 'stock')->get(),
            'teams'     => Team::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'source'      => 'required|in:warehouse,team',
            'team_id'     => 'nullable|required_if:source,team|exists:teams,id',
            'qty'         => 'required|integer|min:1',
            'cause'       => 'required|string',
            'notes'       => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {

                $material = Material::lockForUpdate()
                    ->findOrFail($validated['material_id']);

                // ===== GUDANG =====
                if ($validated['source'] === 'warehouse') {

                    if ($material->stock < $validated['qty']) {
                        throw new \Exception(
                            "Stok gudang {$material->material_name} tidak mencukupi"
                        );
                    }

                    $material->decrement('stock', $validated['qty']);

                    // ===== TIM =====
                } else {

                    $teamStock = TeamStock::lockForUpdate()
                        ->where('team_id', $validated['team_id'])
                        ->where('material_id', $validated['material_id'])
                        ->first();

                    if (!$teamStock || $teamStock->quantity < $validated['qty']) {
                        throw new \Exception("Stok tim tidak mencukupi");
                    }

                    $teamStock->decrement('quantity', $validated['qty']);
                }

                DamagedMaterial::create([
                    'material_id' => $validated['material_id'],
                    'source'      => $validated['source'],
                    'team_id'     => $validated['source'] === 'team'
                        ? $validated['team_id']
                        : null,
                    'qty'         => $validated['qty'],
                    'cause'       => $validated['cause'],
                    'notes'       => $validated['notes'],
                    'reported_by' => Auth::id(),
                    'reported_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['stock' => $e->getMessage()]);
        }

        return redirect()
            ->route('damaged-material.index')
            ->with('success', 'Laporan material rusak berhasil dibuat.');
    }

    public function show(DamagedMaterial $damagedMaterial)
    {
        if (
            Auth::user()->role === 'teknisi' &&
            $damagedMaterial->team_id !== Auth::user()->team_id
        ) {
            abort(403);
        }

        return view('damaged_material.show', compact('damagedMaterial'));
    }


    // 🔥 OPSI 1: DELETE = BALIKIN STOK
    public function destroy(DamagedMaterial $damagedMaterial)
    {
        DB::transaction(function () use ($damagedMaterial) {

            if ($damagedMaterial->source === 'warehouse') {

                Material::where('id', $damagedMaterial->material_id)
                    ->increment('stock', $damagedMaterial->qty);
            } else {

                TeamStock::where('team_id', $damagedMaterial->team_id)
                    ->where('material_id', $damagedMaterial->material_id)
                    ->increment('quantity', $damagedMaterial->qty);
            }

            $damagedMaterial->delete();
        });

        return redirect()
            ->route('damaged-material.index')
            ->with('success', 'Laporan dihapus & stok dikembalikan.');
    }
}
