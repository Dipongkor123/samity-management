<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanRepayment extends Model
{
    protected $fillable = [
        'loan_id', 'amount_paid', 'principal', 'interest', 'paid_date', 'note',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'principal'   => 'decimal:2',
            'interest'    => 'decimal:2',
            'paid_date'   => 'date',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }
}
