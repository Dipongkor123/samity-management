<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deposit extends Model
{
    protected $fillable = [
        'samity_id', 'user_id', 'amount',
        'deposit_date', 'status', 'note', 'receipt_number',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'deposit_date' => 'date',
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
