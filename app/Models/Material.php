<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_code',
        'material_name',
        'category',
        'unit',
        'stock', // 🔥 stok gudang
        'description',
    ];

    public function stocks()
    {
        return $this->hasMany(TeamStock::class);
    }

    public function outDetails()
    {
        return $this->hasMany(MaterialOutDetail::class);
    }
}
