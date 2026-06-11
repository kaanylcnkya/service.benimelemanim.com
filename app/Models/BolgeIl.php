<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BolgeIl extends Model
{
    protected $table = 'bolge_iller';

    public $timestamps = false;

    protected $fillable = [
        'il_adi',
    ];

    public function ilceler(): HasMany
    {
        return $this->hasMany(BolgeIlce::class, 'il_id');
    }
}