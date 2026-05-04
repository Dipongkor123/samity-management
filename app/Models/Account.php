<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends Model
{
    protected $fillable = [
        'type', 'category', 'reference', 'amount',
        'transaction_date', 'description', 'user_id', 'samity_id',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function samity(): BelongsTo
    {
        return $this->belongsTo(Samity::class);
    }

    public static function categories(): array
    {
        return [
            'income'  => ['loan_repayment', 'deposit', 'fine_collection', 'savings_deposit', 'membership_fee', 'other_income'],
            'expense' => ['loan_disbursement', 'savings_withdrawal', 'salary', 'rent', 'office_expense', 'other_expense'],
        ];
    }
}
