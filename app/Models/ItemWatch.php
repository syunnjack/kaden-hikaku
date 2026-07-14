<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemWatch extends Model
{
    protected $fillable = [
        'line_user_id',
        'item_code',
        'item_name',
        'last_known_price',
        'last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'last_checked_at' => 'datetime',
        ];
    }

    public function lineUser(): BelongsTo
    {
        return $this->belongsTo(LineUser::class);
    }
}
