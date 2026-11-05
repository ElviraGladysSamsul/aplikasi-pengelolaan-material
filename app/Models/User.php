<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use App\Models\MaterialUsage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'team_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function materialIns()
    {
        return $this->hasMany(MaterialIn::class);
    }

    public function materialOuts()
    {
        return $this->hasMany(MaterialOut::class);
    }

    public function updatedTeamStocks()
    {
        return $this->hasMany(TeamStock::class, 'updated_by');
    }

    // ✅ TAMBAHAN PENTING
    public function materialUsages()
    {
        return $this->hasMany(MaterialUsage::class, 'technician_id');
    }

    public function damagedMaterials()
    {
        return $this->hasMany(DamagedMaterial::class, 'reported_by');
        // ganti 'user_id' kalau FK beda
    }

    // 🔁 PENGEMBALIAN MATERIAL
    public function materialReturns()
    {
        return $this->hasMany(MaterialReturn::class, 'returned_by');
        // ganti 'user_id' kalau FK beda
    }
}
