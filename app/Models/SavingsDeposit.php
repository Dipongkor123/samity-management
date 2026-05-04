<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingsDeposit extends Model
{
    protected $fillable = [
        'savings_plan_id', 'samity_id', 'user_id',
        'amount', 'deposit_date', 'receipt_number', 'status', 'note',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'deposit_date' => 'date',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SavingsPlan::class, 'savings_plan_id');
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
