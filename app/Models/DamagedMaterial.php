<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamagedMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'source',
        'team_id',
        'qty',
        'cause',
        'reported_by',
        'status',
        'notes',
        'reported_at',
    ];

    // PENTING: Auto-cast ke Carbon
    protected $casts = [
        'reported_at' => 'datetime',
    ];
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
