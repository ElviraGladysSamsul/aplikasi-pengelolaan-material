<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'material_id',
        'reported_by_team',
        'returned_by',
        'qty',
        'notes',
        'returned_at',
    ];

    // PENTING: Auto-cast ke Carbon
    protected $casts = [
        'returned_at' => 'datetime',
    ];
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }
}
