<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    // =======================
    // LIST + SEARCH + FILTER
    // =======================
    public function index(Request $request)
    {
        $query = Material::withCount(['outDetails', 'stocks']);

        // LIVE SEARCH
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('material_name', 'like', "%{$request->q}%")
                    ->orWhere('material_code', 'like', "%{$request->q}%");
            });
        }

        // FILTER CATEGORY
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // PAGINATION
        $materials = $query->orderBy('material_name')
            ->paginate(5)
            ->withQueryString();

        // ambil kategori unik
        $categories = Material::select('category')->distinct()->pluck('category');

        return view('materials.index', compact('materials', 'categories'));
    }

    // =======================
    // FORM TAMBAH
    // =======================
    public function create()
    {
        return view('materials.create');
    }

    // =======================
    // SIMPAN
    // =======================
    public function store(Request $request)
    {
        $request->validate([
            'material_code' => 'required|unique:materials',
            'material_name' => 'required',
            'category'      => 'required',
            'unit'          => 'required',
        ]);

        Material::create([
            'material_code' => $request->material_code,
            'material_name' => $request->material_name,
            'category'      => $request->category,
            'unit'          => $request->unit,
            'stock'         => $request->stock ?? 0,
            'description'   => $request->description,
        ]);

        return redirect()->route('materials.index')
            ->with('success', 'Material berhasil ditambahkan!');
    }

    // =======================
    // UPDATE
    // =======================
    public function update(Request $request, $id)
    {
        $request->validate([
            'material_code' => 'required|unique:materials,material_code,' . $id,
            'material_name' => 'required',
            'category'      => 'required',
            'unit'          => 'required',
        ]);

        $material = Material::findOrFail($id);

        $material->update([
            'material_code' => $request->material_code,
            'material_name' => $request->material_name,
            'category'      => $request->category,
            'unit'          => $request->unit,
            'stock'         => $request->stock ?? 0,
            'description'   => $request->description,
        ]);

        return redirect()->route('materials.index')
            ->with('success', 'Material berhasil diperbarui!');
    }

    // =======================
    // HAPUS
    // =======================
    public function destroy($id)
    {
        $material = Material::withCount(['outDetails', 'stocks'])->findOrFail($id);

        if ($material->out_details_count > 0 || $material->stocks_count > 0) {
            return redirect()->back()
                ->with('error', 'Material ini sedang dipakai, tidak bisa dihapus.');
        }

        $material->delete();

        return redirect()->route('materials.index')
            ->with('success', 'Material berhasil dihapus!');
    }
}
