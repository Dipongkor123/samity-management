<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    protected $fillable = [
        'samity_id', 'user_id', 'amount', 'interest_rate', 'interest_type',
        'duration_months', 'monthly_installment',
        'issue_date', 'due_date', 'status', 'purpose',
    ];

    protected function casts(): array
    {
        return [
            'amount'              => 'decimal:2',
            'interest_rate'       => 'decimal:2',
            'monthly_installment' => 'decimal:2',
            'issue_date'          => 'date',
            'due_date'            => 'date',
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

    public function schedules(): HasMany
    {
        return $this->hasMany(LoanSchedule::class)->orderBy('installment_no');
    }

    public function totalPaid(): float
    {
        return (float) $this->repayments()->sum('amount_paid');
    }

    public function remainingBalance(): float
    {
        return (float) $this->amount - $this->totalPaid();
    }

    /**
     * Calculate flat-interest EMI.
     * Rate = flat % of principal charged as total interest.
     */
    public static function calcFlatEmi(float $principal, float $rate, int $months): float
    {
        if ($months <= 0) return 0;
        $totalInterest = $principal * $rate / 100;
        return ($principal + $totalInterest) / $months;
    }

    /**
     * Calculate declining-balance EMI.
     * Rate = annual interest rate %. Monthly rate = rate/12/100.
     */
    public static function calcDecliningEmi(float $principal, float $annualRate, int $months): float
    {
        if ($months <= 0) return 0;
        $r = $annualRate / 100 / 12;
        if ($r == 0) return $principal / $months;
        return $principal * $r * pow(1 + $r, $months) / (pow(1 + $r, $months) - 1);
    }

    /**
     * Generate (or regenerate) the EMI amortization schedule for this loan.
     */
    public function generateSchedule(): void
    {
        $this->schedules()->delete();

        $principal = (float) $this->amount;
        $rate      = (float) $this->interest_rate;
        $months    = (int)   $this->duration_months;
        $type      = $this->interest_type ?? 'flat';
        $startDate = $this->issue_date ?? now();

        if ($months <= 0 || $principal <= 0) return;

        if ($type === 'flat') {
            $totalInterest  = $principal * $rate / 100;
            $emi            = ($principal + $totalInterest) / $months;
            $principalPart  = $principal / $months;
            $interestPart   = $totalInterest / $months;
            $balance        = $principal;

            for ($i = 1; $i <= $months; $i++) {
                $closing = max(0, round($balance - $principalPart, 2));
                // Last installment absorbs rounding remainder
                $actualPrincipal = ($i === $months) ? $balance : round($principalPart, 2);
                $actualClosing   = ($i === $months) ? 0        : $closing;

                $this->schedules()->create([
                    'installment_no'  => $i,
                    'due_date'        => $startDate->copy()->addMonths($i),
                    'opening_balance' => round($balance, 2),
                    'emi_amount'      => round($actualPrincipal + round($interestPart, 2), 2),
                    'principal'       => round($actualPrincipal, 2),
                    'interest'        => round($interestPart, 2),
                    'closing_balance' => $actualClosing,
                    'status'          => 'pending',
                ]);
                $balance = $actualClosing;
            }
        } else {
            // Declining / reducing balance
            $r   = $rate / 100 / 12;
            $emi = $r > 0
                ? $principal * $r * pow(1 + $r, $months) / (pow(1 + $r, $months) - 1)
                : $principal / $months;

            $balance = $principal;
            for ($i = 1; $i <= $months; $i++) {
                $interest        = round($balance * $r, 2);
                $principalPart   = ($i === $months) ? $balance : round($emi - $interest, 2);
                $closing         = ($i === $months) ? 0        : round(max(0, $balance - $principalPart), 2);

                $this->schedules()->create([
                    'installment_no'  => $i,
                    'due_date'        => $startDate->copy()->addMonths($i),
                    'opening_balance' => round($balance, 2),
                    'emi_amount'      => round($principalPart + $interest, 2),
                    'principal'       => $principalPart,
                    'interest'        => $interest,
                    'closing_balance' => $closing,
                    'status'          => 'pending',
                ]);
                $balance = $closing;
            }
        }
    }
}
