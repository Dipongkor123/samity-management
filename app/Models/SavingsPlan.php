<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SavingsPlan extends Model
{
    protected $fillable = [
        'samity_id', 'user_id', 'plan_type', 'target_amount',
        'regular_amount', 'start_date', 'end_date', 'status', 'note',
    ];

    protected function casts(): array
    {
        return [
            'target_amount'  => 'decimal:2',
            'regular_amount' => 'decimal:2',
            'start_date'     => 'date',
            'end_date'       => 'date',
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

    public function deposits(): HasMany
    {
        return $this->hasMany(SavingsDeposit::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(SavingsWithdrawal::class);
    }

    public function totalDeposited(): float
    {
        return (float) $this->deposits()->where('status', 'paid')->sum('amount');
    }

    public function totalWithdrawn(): float
    {
        return (float) $this->withdrawals()->where('status', 'approved')->sum('amount');
    }

    public function balance(): float
    {
        return $this->totalDeposited() - $this->totalWithdrawn();
    }
}
