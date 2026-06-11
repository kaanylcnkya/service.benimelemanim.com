<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobRequest extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'service_type',
        'city_id',
        'district_id',
        'address_detail',
        'work_date',
        'work_time',
        'description',
        'budget',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(BolgeIl::class, 'city_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(BolgeIlce::class, 'district_id');
    }
}