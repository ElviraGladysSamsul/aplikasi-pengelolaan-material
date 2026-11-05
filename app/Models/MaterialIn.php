<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialIn extends Model
{
    use HasFactory;

    protected $table = 'material_in';

    protected $fillable = [
        'material_id',
        'qty',
        'source',
        'invoice_number',
        'user_id',
        'received_at',
    ];

    // PENTING: Auto-cast ke Carbon
    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
