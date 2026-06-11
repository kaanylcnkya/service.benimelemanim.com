<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CleanerProfile extends Model
{
    protected $fillable = [
        'user_id',
        'services',
        'experience',
        'daily_price',
        'description',
        'is_verified',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'is_verified' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}