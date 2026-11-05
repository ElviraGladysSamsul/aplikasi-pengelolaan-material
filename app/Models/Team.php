<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_name',
        'mitra',
        'description',
    ];

    /* ================= RELATIONS ================= */

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function stocks()
    {
        return $this->hasMany(TeamStock::class);
    }

    public function materialUsages()
    {
        return $this->hasMany(MaterialUsage::class);
    }

    public function materialReturns()
    {
        return $this->hasMany(MaterialReturn::class);
    }

    public function damagedMaterials()
    {
        return $this->hasMany(DamagedMaterial::class);
    }

    /* ================= LOGIC ================= */

    /**
     * Team sudah dipakai di data lain?
     */
    public function isUsed(): bool
    {
        return
            $this->users()->exists() ||
            $this->stocks()->exists() ||
            $this->materialUsages()->exists() ||
            $this->materialReturns()->exists() ||
            $this->damagedMaterials()->exists();
    }
}
