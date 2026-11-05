<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialOutDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_out_id',
        'material_id',
        'quantity',
        'condition',
    ];

    public function materialOut() {
        return $this->belongsTo(MaterialOut::class);
    }

    public function material() {
        return $this->belongsTo(Material::class);
    }
}
