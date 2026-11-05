<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role === 'teknisi') {
            abort(403);
        }

        $teams = Team::query()
            ->when($request->q, function ($q) use ($request) {
                $q->where('team_name', 'like', "%{$request->q}%")
                    ->orWhere('mitra', 'like', "%{$request->q}%");
            })
            ->when($request->mitra, function ($q) use ($request) {
                $q->where('mitra', $request->mitra);
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $mitras = Team::select('mitra')
            ->whereNotNull('mitra')
            ->distinct()
            ->pluck('mitra', 'mitra');

        return view('teams.index', compact('teams', 'mitras'));
    }

    public function create()
    {
        return view('teams.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'team_name'   => 'required|string|max:255',
            'mitra'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Team::create($request->only([
            'team_name',
            'mitra',
            'description',
        ]));

        return redirect()
            ->route('teams.index')
            ->with('success', 'Team berhasil ditambahkan.');
    }

    public function show(Team $team)
    {
        $team->load([
            'users',
            'stocks.material',
        ]);

        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        return view('teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $request->validate([
            'team_name'   => 'required|string|max:255',
            'mitra'       => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $team->update($request->only([
            'team_name',
            'mitra',
            'description',
        ]));

        return redirect()
            ->route('teams.index')
            ->with('success', 'Team berhasil diperbarui.');
    }

    public function destroy(Team $team)
    {
        // 🔒 PROTEKSI UTAMA
        if ($team->isUsed()) {
            return back()->withErrors(
                'Team tidak bisa dihapus karena sudah digunakan di data lain.'
            );
        }

        $team->delete();

        return redirect()
            ->route('teams.index')
            ->with('success', 'Team berhasil dihapus.');
    }
}
