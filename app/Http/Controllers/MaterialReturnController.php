<?php

namespace App\Http\Controllers;

use App\Models\MaterialReturn;
use App\Models\Material;
use App\Models\Team;
use App\Models\TeamStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\MaterialReturnExport;
use Maatwebsite\Excel\Facades\Excel;

class MaterialReturnController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialReturn::with(['material', 'team',  'admin'])
            ->latest();

        if (Auth::user()->role === 'teknisi') {
            $query->where('team_id', Auth::user()->team_id);
        }

        // 🔍 search material / team
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas(
                'material',
                fn($m) =>
                $m->where('material_name', 'like', "%$q%")
            )->orWhereHas(
                'team',
                fn($t) =>
                $t->where('team_name', 'like', "%$q%")
            );
        }

        // FILTER DATE RANGE
        if ($request->filled('date_range')) {
            // Ganti semua tipe separator jadi tanda koma sementara
            $cleaned = str_replace([' to ', ' - ', '–'], ',', $request->date_range);
            $dates = explode(',', $cleaned);

            if (count($dates) === 2) {
                $start = trim($dates[0]) . ' 00:00:00';
                $end   = trim($dates[1]) . ' 23:59:59';
                $query->whereBetween('returned_at', [$start, $end]);
            }
        }

        $data = $query->paginate(5)->withQueryString();

        return view('material_return.index', compact('data'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new MaterialReturnExport($request),
            'material-returns.xlsx'
        );
    }
    public function create()
    {
        return view('material_return.create', [
            'teams'     => Team::all(),
            'materials' => Material::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id'           => 'required|exists:teams,id',
            'material_id'       => 'required|exists:materials,id',
            'qty'               => 'required|integer|min:1',
            'reported_by_team'  => 'nullable|string',
            'notes'             => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($validated) {

                // 🔒 kunci stok tim
                $teamStock = TeamStock::lockForUpdate()
                    ->where('team_id', $validated['team_id'])
                    ->where('material_id', $validated['material_id'])
                    ->first();

                if (!$teamStock || $teamStock->quantity < $validated['qty']) {
                    throw new \Exception('Stok tim tidak mencukupi');
                }

                // 🔒 kunci material gudang
                $material = Material::lockForUpdate()
                    ->findOrFail($validated['material_id']);

                // ➖ kurangi stok tim
                $teamStock->decrement('quantity', $validated['qty']);

                // ➕ tambah stok gudang
                $material->increment('stock', $validated['qty']);

                // 💾 simpan return
                MaterialReturn::create([
                    'team_id'          => $validated['team_id'],
                    'material_id'      => $validated['material_id'],
                    'qty'              => $validated['qty'],
                    'reported_by_team' => $validated['reported_by_team'],
                    'notes'            => $validated['notes'],
                    'returned_by'      => Auth::id(),
                    'returned_at'      => now(),
                ]);
            });
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->withErrors(['stock' => $e->getMessage()]);
        }

        return redirect()
            ->route('material-returns.index')
            ->with('success', 'Material berhasil dikembalikan ke gudang');
    }

    public function show(MaterialReturn $materialReturn)
    {
        $materialReturn->load('team', 'material', 'admin');
        return view('material_return.show', compact('materialReturn'));
    }

    /**
     * ❌ TIDAK ADA EDIT
     * kalau salah → hapus
     */

    public function destroy(MaterialReturn $materialReturn)
    {
        DB::transaction(function () use ($materialReturn) {

            // rollback stok
            $materialReturn->material->decrement('stock', $materialReturn->qty);

            TeamStock::where('team_id', $materialReturn->team_id)
                ->where('material_id', $materialReturn->material_id)
                ->increment('quantity', $materialReturn->qty);

            $materialReturn->delete();
        });

        return back()->with('success', 'Data return dihapus & stok dikembalikan');
    }
}
