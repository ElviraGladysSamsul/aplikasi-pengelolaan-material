<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\MaterialInExport;
use Maatwebsite\Excel\Facades\Excel;

class MaterialInController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialIn::with('material', 'user');

        // SEARCH MATERIAL
        if ($request->filled('q')) {
            $query->whereHas('material', function ($q) use ($request) {
                $q->where('material_name', 'like', '%' . $request->q . '%');
            });
        }

        // FILTER DATE RANGE
        if ($request->filled('date_range')) {
            // Ganti semua tipe separator jadi tanda koma sementara
            $cleaned = str_replace([' to ', ' - ', '–'], ',', $request->date_range);
            $dates = explode(',', $cleaned);

            if (count($dates) === 2) {
                $start = trim($dates[0]) . ' 00:00:00';
                $end   = trim($dates[1]) . ' 23:59:59';
                $query->whereBetween('received_at', [$start, $end]);
            }
        }

        $materialsIn = $query->orderBy('received_at', 'desc')
            ->paginate(5)
            ->withQueryString();

        return view('material_in.index', compact('materialsIn'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new MaterialInExport($request),
            'material_masuk_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function create()
    {
        return view('material_in.create', [
            'materials' => Material::orderBy('material_name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'qty'         => 'required|integer|min:1',
            'source'      => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $request) {
            MaterialIn::create([
                'material_id'    => $validated['material_id'],
                'qty'            => $validated['qty'],
                'source'         => $validated['source'],
                'invoice_number' => $request->invoice_number,
                'user_id'        => Auth::id(),
                'received_at'    => $request->received_at ?? now(),
            ]);

            $material = Material::lockForUpdate()->findOrFail($validated['material_id']);
            $material->increment('stock', $validated['qty']);
        });

        return redirect()->route('material-in.index')
            ->with('success', 'Material masuk berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $item = MaterialIn::lockForUpdate()->findOrFail($id);
                $material = Material::lockForUpdate()->findOrFail($item->material_id);

                if ($material->stock < $item->qty) {
                    throw new \Exception('Tidak bisa menghapus, stok akan menjadi minus');
                }

                $material->decrement('stock', $item->qty);
                $item->delete();
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['stock' => $e->getMessage()]);
        }

        return redirect()->route('material-in.index')
            ->with('success', 'Data material masuk dihapus.');
    }
}
