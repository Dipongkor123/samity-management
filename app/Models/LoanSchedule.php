<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanSchedule extends Model
{
    protected $fillable = [
        'loan_id', 'installment_no', 'due_date',
        'opening_balance', 'emi_amount', 'principal', 'interest',
        'closing_balance', 'status',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'decimal:2',
            'emi_amount'      => 'decimal:2',
            'principal'       => 'decimal:2',
            'interest'        => 'decimal:2',
            'closing_balance' => 'decimal:2',
            'due_date'        => 'date',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
