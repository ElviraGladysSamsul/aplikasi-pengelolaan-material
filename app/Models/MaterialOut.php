<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'distributed_at',
        'notes',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(MaterialOutDetail::class);
    }
}
