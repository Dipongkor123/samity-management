<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    protected $fillable = [
        'samity_id', 'user_id', 'reason',
        'amount', 'fine_date', 'status', 'note',
    ];

    protected function casts(): array
    {
        return [
            'amount'    => 'decimal:2',
            'fine_date' => 'date',
        ];
    }

    public function samity(): BelongsTo
    {
        return $this->belongsTo(Samity::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
