<?php

namespace App\Http\Controllers;

use App\Models\TeamStock;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamStockController extends Controller
{
    public function index()
    {
        $teamStocks = TeamStock::with(['team', 'material', 'updater'])
            ->orderBy('team_id')
            ->orderBy('material_id')
            ->get();

        return view('team-stocks.index', compact('teamStocks'));
    }

    public function showTeam($teamId)
    {
        $team = Team::findOrFail($teamId);

        $stocks = TeamStock::with(['material', 'updater'])
            ->where('team_id', $teamId)
            ->get();

        return view('team-stocks.show-team', compact('team', 'stocks'));
    }
}
