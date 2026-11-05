<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exports\UserExport;
use App\Imports\UserImport;
use Maatwebsite\Excel\Facades\Excel;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('team')->latest();

        // 🔍 SEARCH nama / email
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // 🎭 FILTER ROLE
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(5)->withQueryString();

        $roles = [
            'superadmin' => 'Superadmin',
            'admin'      => 'Admin',
            'supervisor' => 'Supervisor',
            'teknisi'    => 'Teknisi',
        ];

        return view('users.index', compact('users', 'roles'));
    }

    public function template()
    {
        abort_unless(in_array(Auth::user()->role, ['superadmin', 'admin']), 403);
        return Excel::download(new UserExport(true), 'template-user.xlsx');
    }

    public function import(Request $request)
    {
        abort_unless(in_array(Auth::user()->role, ['superadmin', 'admin']), 403);

        $request->validate([
            'file' => 'required|mimes:xlsx'
        ]);

        Excel::import(new UserImport, $request->file('file'));

        return back()->with('success', 'Import user berhasil.');
    }

    public function export(Request $request)
    {
        abort_unless(in_array(Auth::user()->role, ['superadmin', 'admin', 'supervisor']), 403);

        return Excel::download(
            new UserExport(false, $request),
            'users.xlsx'
        );
    }


    public function create()
    {
        $teams = Team::all();
        $roles = ['superadmin', 'admin', 'supervisor', 'teknisi'];
        return view('users.create', compact('teams', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:superadmin,admin,supervisor,teknisi',
            'team_id'  => 'nullable|exists:teams,id',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'team_id'  => $request->team_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $teams = Team::all();
        $roles = ['superadmin', 'admin', 'supervisor', 'teknisi'];
        return view('users.edit', compact('user', 'teams', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:superadmin,admin,supervisor,teknisi',
            'team_id'  => 'nullable|exists:teams,id',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'role'    => $request->role,
            'team_id' => $request->team_id,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        // 🚫 tidak boleh hapus diri sendiri
        if (Auth::id() === $user->id) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat menghapus akun anda sendiri.');
        }

        // 🚫 cek apakah user sudah dipakai
        $isUsed =
            $user->materialIns()->exists() ||
            $user->materialOuts()->exists() ||
            $user->updatedTeamStocks()->exists() ||
            $user->materialUsages()->exists();
        $user->damagedMaterials()->exists() ||
            $user->materialReturns()->exists();

        if ($isUsed) {
            return redirect()->back()
                ->with('error', 'User ini sudah digunakan pada data lain dan tidak dapat dihapus.');
        }

        // ✅ aman dihapus
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
