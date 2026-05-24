<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\ApprovalConfig;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ProcessRecurringTransactions extends Command
{
    protected $signature = 'recurring:process';
    protected $description = 'Process due recurring transactions and create transaction records';

    public function handle()
    {
        $today = Carbon::today();

        $due = RecurringTransaction::where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('last_generated_date')
                  ->orWhere(function ($q2) use ($today) {
                      $q2->where('frequency', 'daily')
                         ->whereDate('last_generated_date', '<', $today);
                  })
                  ->orWhere(function ($q2) use ($today) {
                      $q2->where('frequency', 'weekly')
                         ->whereDate('last_generated_date', '<=', $today->copy()->subDays(7));
                  })
                  ->orWhere(function ($q2) use ($today) {
                      $q2->where('frequency', 'monthly')
                         ->whereDate('last_generated_date', '<=', $today->copy()->subMonth());
                  })
                  ->orWhere(function ($q2) use ($today) {
                      $q2->where('frequency', 'yearly')
                         ->whereDate('last_generated_date', '<=', $today->copy()->subYear());
                  });
            })
            ->whereDate('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', $today);
            })
            ->get();

        $count = 0;
        foreach ($due as $recurring) {
            $config = ApprovalConfig::where('company_id', $recurring->company_id)
                ->where('is_active', true)
                ->where('min_amount', '<=', $recurring->amount)
                ->where(function ($q) use ($recurring) {
                    $q->whereNull('max_amount')
                      ->orWhere('max_amount', '>=', $recurring->amount);
                })
                ->first();

            $status = ($config && $config->requires_level_1) ? 'pending' : 'approved';

            $transaction = Transaction::create([
                'company_id' => $recurring->company_id,
                'user_id' => $recurring->user_id,
                'category_id' => $recurring->category_id,
                'type' => $recurring->type,
                'amount' => $recurring->amount,
                'description' => $recurring->description . ' (Otomatis)',
                'payment_method' => $recurring->payment_method ?? 'cash',
                'transaction_date' => $today,
                'status' => $status,
                'is_recurring' => true,
            ]);

            $recurring->update(['last_generated_date' => $today]);
            $count++;
        }

        $this->info("Processed {$count} recurring transaction(s).");

        return Command::SUCCESS;
    }
}
