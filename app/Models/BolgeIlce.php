<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BolgeIlce extends Model
{
    protected $table = 'bolge_ilceler';

    public $timestamps = false;

    protected $fillable = [
        'il_id',
        'ilce_adi',
    ];

    public function il(): BelongsTo
    {
        return $this->belongsTo(BolgeIl::class, 'il_id');
    }
}