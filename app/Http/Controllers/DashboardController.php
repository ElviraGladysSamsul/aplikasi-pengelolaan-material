<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/* MODELS */
use App\Models\Material;
use App\Models\MaterialIn;
use App\Models\MaterialOut;
use App\Models\MaterialUsage;
use App\Models\MaterialReturn;
use App\Models\DamagedMaterial;
use App\Models\Team;
use App\Models\User;
use App\Models\TeamStock;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isTeknisi = $user->role === 'teknisi';
        $teamId = $user->team_id;

        /* ===============================
        | FILTER TANGGAL
        =============================== */
        $startDate = $request->start_date
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->end_date
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfMonth();

        /* ===============================
        | FILTER TIM (NON TEKNISI)
        =============================== */
        $teams = $isTeknisi ? [] : Team::all();
        $selectedTeamId = $request->team_id;

        if (!$isTeknisi && $selectedTeamId) {
            $teamId = $selectedTeamId; // override untuk query chart
        }

        /* ===============================
        | MATERIAL (STOCK)
        =============================== */
        if ($isTeknisi || $selectedTeamId) {
            $totalMaterial = TeamStock::where('team_id', $teamId)->count();
            $materialAvailable = TeamStock::where('team_id', $teamId)->where('quantity', '>', 0)->count();
            $materialLowStock = TeamStock::where('team_id', $teamId)->where('quantity', '<=', 5)->count();
        } else {
            $totalMaterial = Material::count();
            $materialAvailable = Material::where('stock', '>', 0)->count();
            $materialLowStock = Material::where('stock', '<=', 5)->count();
        }

        /* ===============================
        | TRANSAKSI
        =============================== */
        $materialIn = $isTeknisi || $selectedTeamId
            ? MaterialOut::where('team_id', $teamId)->whereBetween('created_at', [$startDate, $endDate])->count()
            : MaterialIn::whereBetween('received_at', [$startDate, $endDate])->count();

        $distribution = (!$isTeknisi && !$selectedTeamId)
            ? MaterialOut::whereBetween('created_at', [$startDate, $endDate])->count()
            : null;

        $usage = MaterialUsage::when($isTeknisi || $selectedTeamId, fn($q) => $q->where('team_id', $teamId))
            ->when(!$isTeknisi && !$selectedTeamId, fn($q) => $q) // semua tim
            ->whereBetween('created_at', [$startDate, $endDate])->count();

        $return = MaterialReturn::when($isTeknisi || $selectedTeamId, fn($q) => $q->where('team_id', $teamId))
            ->when(!$isTeknisi && !$selectedTeamId, fn($q) => $q)
            ->whereBetween('returned_at', [$startDate, $endDate])->count();

        $damaged = DamagedMaterial::when($isTeknisi || $selectedTeamId, fn($q) => $q->where('team_id', $teamId))
            ->when(!$isTeknisi && !$selectedTeamId, fn($q) => $q)
            ->whereBetween('reported_at', [$startDate, $endDate])->count();

        /* ===============================
        | TEAM & USER
        =============================== */
        if ($isTeknisi || $selectedTeamId) {
            $totalTeamOrUser = User::where('team_id', $teamId)->count();
            $totalUser = null;
        } else {
            $totalTeamOrUser = Team::count();
            $totalUser = User::count();
        }

        return view('dashboard.index', compact(
            'isTeknisi',
            'startDate',
            'endDate',
            'totalMaterial',
            'materialAvailable',
            'materialLowStock',
            'materialIn',
            'distribution',
            'usage',
            'return',
            'damaged',
            'totalTeamOrUser',
            'totalUser',
            'teams',
            'selectedTeamId'
        ));
    }
}
