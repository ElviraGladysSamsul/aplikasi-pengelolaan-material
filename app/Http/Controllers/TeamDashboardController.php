<?php

namespace App\Http\Controllers;

use App\Models\MaterialOut;
use App\Models\MaterialIn;
use App\Models\Team;
use App\Models\TeamStock;
use Illuminate\Http\Request;

class TeamDashboardController extends Controller
{
    public function index(Team $team)
    {
        // Total stok tim
        $total_stok = TeamStock::where('team_id', $team->id)->sum('quantity');

        // Total barang masuk untuk tim ini
        $total_masuk = MaterialIn::where('team_id', $team->id)->count();

        // Total barang keluar untuk tim ini
        $total_keluar = MaterialOut::where('team_id', $team->id)->count();

        // Anggota tim
        $anggota = $team->users;

        // Grafik penggunaan (ambil 7 hari terakhir)
        $grafik = MaterialOut::where('team_id', $team->id)
            ->selectRaw('DATE(created_at) as tanggal, COUNT(*) as jumlah')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->limit(7)
            ->get();

        return view('teams.dashboard', compact(
            'team',
            'total_stok',
            'total_masuk',
            'total_keluar',
            'anggota',
            'grafik'
        ));
    }
}
