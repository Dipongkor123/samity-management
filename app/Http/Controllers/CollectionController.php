<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\LoanSchedule;
use App\Models\Samity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CollectionController extends Controller
{
    /**
     * Bulk collection entry: pick a samity + date, see all active loan members.
     */
    public function bulk(Request $request)
    {
        $samities    = Samity::where('is_active', true)->orderBy('name')->get();
        $activeLoans = collect();
        $selectedSamity = null;

        if ($request->filled('samity_id')) {
            $selectedSamity = Samity::findOrFail($request->samity_id);
            $activeLoans = Loan::with(['user', 'schedules'])
                ->where('samity_id', $request->samity_id)
                ->where('status', 'active')
                ->orderBy('id')
                ->get()
                ->map(function ($loan) {
                    $loan->next_due = $loan->schedules
                        ->where('status', '!=', 'paid')
                        ->sortBy('installment_no')
                        ->first();
                    return $loan;
                });
        }

        return view('collection.bulk', compact('samities', 'activeLoans', 'selectedSamity'));
    }

    /**
     * Save bulk repayments submitted from the bulk form.
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'collection_date' => ['required', 'date'],
            'payments'        => ['required', 'array'],
            'payments.*.loan_id'    => ['required', 'exists:loans,id'],
            'payments.*.amount_paid'=> ['required', 'numeric', 'min:0.01'],
        ]);

        $date = $request->collection_date;
        $saved = 0;

        DB::transaction(function () use ($request, $date, &$saved) {
            foreach ($request->payments as $row) {
                if (empty($row['amount_paid']) || $row['amount_paid'] <= 0) continue;

                $loan = Loan::find($row['loan_id']);
                if (! $loan || $loan->status === 'completed') continue;

                LoanRepayment::create([
                    'loan_id'     => $loan->id,
                    'amount_paid' => $row['amount_paid'],
                    'paid_date'   => $date,
                    'note'        => $row['note'] ?? null,
                ]);

                // Mark next pending schedule as paid
                $schedule = $loan->schedules()
                                 ->where('status', '!=', 'paid')
                                 ->orderBy('installment_no')
                                 ->first();
                if ($schedule) {
                    $schedule->update(['status' => 'paid', 'paid_date' => $date]);
                }

                // Check if loan is now fully repaid
                $totalPaid = (float) $loan->repayments()->sum('amount_paid');
                if ($totalPaid >= (float) $loan->amount) {
                    $loan->update(['status' => 'completed']);
                }

                $saved++;
            }
        });

        return redirect()->route('collection.daily', ['date' => $date])
                         ->with('success', "$saved repayment(s) recorded successfully.");
    }

    /**
     * Daily collection summary.
     */
    public function daily(Request $request)
    {
        $date     = $request->input('date', now()->toDateString());
        $samities = Samity::where('is_active', true)->orderBy('name')->get();

        $query = LoanRepayment::with(['loan.user', 'loan.samity'])
                              ->whereDate('paid_date', $date);

        if ($request->filled('samity_id')) {
            $query->whereHas('loan', fn($q) => $q->where('samity_id', $request->samity_id));
        }

        $collections = $query->latest('id')->get();

        $totalCollected = $collections->sum('amount_paid');
        $memberCount    = $collections->pluck('loan.user_id')->unique()->count();

        // Overdue installments as of today
        $overdueQuery = LoanSchedule::with(['loan.user', 'loan.samity'])
            ->where('status', '!=', 'paid')
            ->whereDate('due_date', '<', now()->toDateString());

        if ($request->filled('samity_id')) {
            $overdueQuery->whereHas('loan', fn($q) => $q->where('samity_id', $request->samity_id));
        }

        $overdueInstallments = $overdueQuery->orderBy('due_date')->limit(50)->get();

        return view('collection.daily', compact(
            'date', 'collections', 'totalCollected', 'memberCount',
            'samities', 'overdueInstallments'
        ));
    }
}
