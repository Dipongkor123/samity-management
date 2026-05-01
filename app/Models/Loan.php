<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'samity_id', 'user_id', 'amount', 'interest_rate',
        'duration_months', 'monthly_installment',
        'issue_date', 'due_date', 'status', 'purpose',
    ];

    protected function casts(): array
    {
        return [
            'amount'               => 'decimal:2',
            'interest_rate'        => 'decimal:2',
            'monthly_installment'  => 'decimal:2',
            'issue_date'           => 'date',
            'due_date'             => 'date',
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

    public function repayments(): HasMany
    {
        return $this->hasMany(LoanRepayment::class);
    }

    public function totalPaid(): float
    {
        return (float) $this->repayments()->sum('amount_paid');
    }

    public function remainingBalance(): float
    {
        return (float) $this->amount - $this->totalPaid();
    }
}
