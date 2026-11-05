<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| MODELS
|--------------------------------------------------------------------------
*/
use App\Models\Material;
use App\Models\MaterialUsage;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialInController;
use App\Http\Controllers\MaterialOutController;
use App\Http\Controllers\MaterialReturnController;
use App\Http\Controllers\MaterialUsageController;
use App\Http\Controllers\DamagedMaterialController;
use App\Http\Controllers\MaterialExcelController;

use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamStockController;
use App\Http\Controllers\TeamDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeamExcelController;

use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/
// Route::get('/', fn () => view('welcome'));
Route::get('/', fn() => redirect()->route('login'));

/*
|--------------------------------------------------------------------------
| AUTHENTICATED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | MATERIAL (MASTER)
    |--------------------------------------------------------------------------
    */
    Route::get('/materials/check-code', function (Request $request) {
        $exists = Material::where('material_code', $request->material_code)->exists();
        return response()->json(['exists' => $exists]);
    })->name('materials.check-code');

    Route::get('/materials-export', [MaterialExcelController::class, 'export'])
        ->name('materials.export');
    Route::post('/materials-import', [MaterialExcelController::class, 'import'])
        ->name('materials.import');
    Route::get('/materials-template', [MaterialExcelController::class, 'template'])
        ->name('materials.template');

    Route::resource('materials', MaterialController::class);

    /*
    |--------------------------------------------------------------------------
    | MATERIAL IN
    |--------------------------------------------------------------------------
    */
    Route::get('/material-in/export', [MaterialInController::class, 'export'])
        ->middleware('role:superadmin,admin,supervisor')
        ->name('material-in.export');

    Route::resource('material-in', MaterialInController::class);

    /*
    |--------------------------------------------------------------------------
    | MATERIAL OUT / DISTRIBUTION
    |--------------------------------------------------------------------------
    */
    Route::get('/material-out/export', [MaterialOutController::class, 'export'])
        ->middleware('role:superadmin,admin,supervisor')
        ->name('material-out.export');

    Route::resource('material-out', MaterialOutController::class);

    /*
    |--------------------------------------------------------------------------
    | MATERIAL USAGE (WO)
    |--------------------------------------------------------------------------
    */
    Route::get('/material-usages/check-wo', function (Request $request) {
        if (!$request->no_wo) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => MaterialUsage::where('no_wo', $request->no_wo)->exists()
        ]);
    })->name('material-usages.check-wo');

    Route::get('/material-usages/export', [MaterialUsageController::class, 'export'])
        ->middleware('role:superadmin,admin,supervisor')
        ->name('material-usages.export');

    Route::resource('material-usages', MaterialUsageController::class);

    /*
    |--------------------------------------------------------------------------
    | MATERIAL RETURN
    |--------------------------------------------------------------------------
    */
    Route::get('/material-returns/export', [MaterialReturnController::class, 'export'])
        ->middleware('role:superadmin,admin,supervisor')
        ->name('material-returns.export');

    Route::resource('material-returns', MaterialReturnController::class);

    /*
    |--------------------------------------------------------------------------
    | DAMAGED MATERIAL
    |--------------------------------------------------------------------------
    */
    Route::get('/damaged-material/export', [DamagedMaterialController::class, 'export'])
        ->middleware('role:superadmin,admin,supervisor')
        ->name('damaged-material.export');

    Route::resource('damaged-material', DamagedMaterialController::class);

    /*
    |--------------------------------------------------------------------------
    | TEAM
    |--------------------------------------------------------------------------
    */

    // KHUSUS TEKNISI → langsung ke tim sendiri
    Route::get('/team', function () {
        return redirect()->route('teams.show', Auth::user()->team_id);
    })->middleware('role:teknisi');

    Route::get('/teams/{team}/dashboard', [TeamDashboardController::class, 'index'])
        ->name('teams.dashboard');

    Route::get('/teams-export', [TeamExcelController::class, 'export'])
        ->middleware('role:superadmin,admin,supervisor')
        ->name('teams.export');

    Route::post('/teams-import', [TeamExcelController::class, 'import'])
        ->middleware('role:superadmin,admin')
        ->name('teams.import');

    Route::get('/teams-template', [TeamExcelController::class, 'template'])
        ->middleware('role:superadmin,admin')
        ->name('teams.template');

    Route::resource('teams', TeamController::class);

    /*
    |--------------------------------------------------------------------------
    | TEAM STOCK
    |--------------------------------------------------------------------------
    */
    Route::resource('team-stocks', TeamStockController::class);

    Route::get('/team-stocks/team/{teamId}', [TeamStockController::class, 'showTeam'])
        ->name('team-stocks.team');

    /*
    |--------------------------------------------------------------------------
    | USER
    |--------------------------------------------------------------------------
    */
    Route::get('/users/export', [UserController::class, 'export'])
        ->middleware('role:superadmin,admin,supervisor')
        ->name('users.export');

    Route::post('/users/import', [UserController::class, 'import'])
        ->middleware('role:superadmin,admin')
        ->name('users.import');

    Route::get('/users/template', [UserController::class, 'template'])
        ->middleware('role:superadmin,admin')
        ->name('users.template');

    Route::resource('users', UserController::class)
        ->middleware('role:superadmin,admin,supervisor');

    /*
    |--------------------------------------------------------------------------
    | API HELPER
    |--------------------------------------------------------------------------
    */
    Route::get('/api/materials/by-team/{team}', function ($team) {
        return Material::with([
            'stocks' => fn($q) => $q->where('team_id', $team)
        ])->get()->map(fn($m) => [
            'id'    => $m->id,
            'name'  => $m->material_name,
            'stock' => optional($m->stocks->first())->quantity ?? 0,
        ]);
    });
});

require __DIR__ . '/auth.php';
