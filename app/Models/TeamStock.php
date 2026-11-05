<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'material_id',
        'quantity',
        'updated_by',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
