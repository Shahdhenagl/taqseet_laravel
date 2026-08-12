<?php

namespace App\Console\Commands;

use App\Models\Installment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInstallmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installments:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan upcoming unpaid installments due in 3 days and log WhatsApp reminder queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $upcomingInstallments = Installment::with('plan.customer')
            ->where('is_paid', false)
            ->whereBetween('due_date', [now(), now()->addDays(3)])
            ->get();

        $count = $upcomingInstallments->count();
        $this->info("Found {$count} upcoming installments due in the next 3 days.");

        foreach ($upcomingInstallments as $installment) {
            $customer = $installment->plan->customer ?? null;
            if ($customer) {
                $phone = $customer->whatsapp_number ?: $customer->phone_number;
                $msg = "تذكير: عزيزي {$customer->name}، نود تذكيرك بموعد قسط بقيمة {$installment->amount} ج.م والمستحق في {$installment->due_date->format('Y-m-d')}.";
                
                Log::info("WhatsApp Reminder Queued for Customer {$customer->name} ({$phone}): {$msg}");
                $this->line("→ Queued reminder for: {$customer->name} ({$phone})");
            }
        }

        $this->info("WhatsApp Cron Reminders scan completed successfully.");
        return Command::SUCCESS;
    }
}
