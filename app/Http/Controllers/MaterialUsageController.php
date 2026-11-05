<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialUsage;
use App\Models\Team;
use App\Services\MaterialUsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaterialUsageExport;

class MaterialUsageController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 query dasar
        $query = MaterialUsage::with(['team', 'technician']);

        // 🔐 filter role teknisi (hanya tim sendiri)
        if (Auth::user()->role === 'teknisi') {
            $query->where('team_id', Auth::user()->team_id);
        }

        // 🔍 search
        $query->when($request->q, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where('no_wo', 'like', "%{$request->q}%")
                    ->orWhere('mitra', 'like', "%{$request->q}%")
                    ->orWhereHas('technician', function ($t) use ($request) {
                        $t->where('name', 'like', "%{$request->q}%");
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

        // 📦 result
        $usages = $query
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view('material-usages.index', compact('usages'));
    }

    public function export(Request $request)
    {
        return Excel::download(
            new MaterialUsageExport($request),
            'pemakaian-material-wo.xlsx'
        );
    }

    public function create()
    {
        $user = Auth::user();
        // 🔹 SUPERADMIN
        if ($user->role === 'superadmin') {
            $teams = Team::all();
            $materials = collect(); // kosong dulu, isi setelah pilih team

            return view('material-usages.create', [
                'teams'     => $teams,
                'materials' => $materials,
                'mitra'     => null,
            ]);
        }

        // 🔹 USER BIASA
        $teamId = $user->team_id;

        $materials = Material::with(['stocks' => function ($q) use ($teamId) {
            $q->where('team_id', $teamId);
        }])->get()->map(function ($m) {
            return [
                'id'    => $m->id,
                'name'  => $m->material_name,
                'stock' => optional($m->stocks->first())->quantity ?? 0,
            ];
        });

        return view('material-usages.create', [
            'materials' => $materials,
            'mitra'     => $user->team->mitra,
        ]);
    }

    public function store(Request $request, MaterialUsageService $service)
    {
        $user = Auth::user();

        $rules = [
            'no_wo'     => 'required|unique:material_usages,no_wo',
            'jenis_psb' => 'required',
            'sn_ont'    => 'required',

            'barcode_dc'   => 'nullable|string',
            'sn_stb'       => 'nullable|string',
            'no_reservasi' => 'nullable|string',
            'valins_id'    => 'nullable|string',
            'power_rx_ont' => 'nullable|string',
            'catatan_mitra' => 'nullable|string',

            'odp_lat'        => 'required',
            'odp_long'       => 'required',
            'pelanggan_lat'  => 'required',
            'pelanggan_long' => 'required',

            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.quantity'    => 'required|integer|min:1',
        ];

        // 🔥 SUPERADMIN WAJIB PILIH TEAM
        if ($user->role === 'superadmin') {
            $rules['team_id'] = 'required|exists:teams,id';
        }

        $validated = $request->validate($rules);

        // inject team_id
        $validated['team_id'] = $user->role === 'superadmin'
            ? $request->team_id
            : $user->team_id;

        $validated['technician_id'] = $user->id;

        $service->store($validated);

        return redirect()
            ->route('material-usages.index')
            ->with('success', 'Pemakaian material berhasil disimpan');
    }

    public function show(MaterialUsage $materialUsage)
    {
        $materialUsage->load(['items.material', 'team', 'technician']);
        return view('material-usages.show', compact('materialUsage'));
    }

    public function destroy(MaterialUsage $materialUsage, MaterialUsageService $service)
    {
        // optional: proteksi role
        // if (Auth::user()->role !== 'superadmin') {
        //     abort(403);
        // }

        $service->delete($materialUsage);

        return redirect()
            ->route('material-usages.index')
            ->with('success', 'Data pemakaian material berhasil dihapus');
    }
}
