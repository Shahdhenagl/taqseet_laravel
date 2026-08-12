<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Installment;
use App\Notifications\AdminInstallmentDueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotifyAdminUpcomingInstallments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installments:notify-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send admin due installment notifications at 30, 15, 7, 3, 1 days and today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $milestones = [
            30 => 'باقي 30 يوماً',
            15 => 'باقي 15 يوماً',
            7  => 'باقي أسبوع واحد (7 أيام)',
            3  => 'باقي 3 أيام',
            1  => 'مستحق غداً (باقي يوم واحد)',
            0  => 'مستحق اليوم!',
        ];

        $totalSent = 0;
        $admin = Customer::first(); // Send to admin entity

        if (!$admin) {
            $this->warn('No customer/admin record found in database.');
            return Command::SUCCESS;
        }

        foreach ($milestones as $days => $label) {
            $targetDate = Carbon::today()->addDays($days)->format('Y-m-d');

            $installments = Installment::with(['plan.customer'])
                ->where('is_paid', false)
                ->whereDate('due_date', $targetDate)
                ->get();

            foreach ($installments as $installment) {
                // Prevent duplicate notification on same day for same installment and milestone
                $exists = DB::table('notifications')
                    ->where('type', AdminInstallmentDueNotification::class)
                    ->whereRaw("json_extract(data, '$.installment_id') = ?", [$installment->id])
                    ->whereRaw("json_extract(data, '$.days_left') = ?", [$days])
                    ->whereDate('created_at', Carbon::today())
                    ->exists();

                if (!$exists) {
                    $admin->notify(new AdminInstallmentDueNotification($installment, $days, $label));
                    $totalSent++;
                    $customerName = $installment->plan->customer->name ?? 'عميل';
                    $this->line("✔ Sent Admin Notification [{$label}]: {$customerName} - {$installment->amount} EGP (Due {$targetDate})");
                }
            }
        }

        $this->info("Admin Notification Scan Completed. Total new notifications sent: {$totalSent}");
        return Command::SUCCESS;
    }
}
