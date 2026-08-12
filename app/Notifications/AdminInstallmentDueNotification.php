<?php

namespace App\Notifications;

use App\Models\Installment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminInstallmentDueNotification extends Notification
{
    use Queueable;

    public $installment;
    public $daysLeft;
    public $label;

    public function __construct(Installment $installment, int $daysLeft, string $label)
    {
        $this->installment = $installment;
        $this->daysLeft = $daysLeft;
        $this->label = $label;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        $customerName = $this->installment->plan->customer->name ?? 'عميل';
        $amount = number_format($this->installment->amount, 2);
        $dueDate = $this->installment->due_date->format('Y-m-d');

        $title = "تنبيه قسط قادم ({$this->label})";
        $message = "العميل {$customerName} لديه قسط بقيمة {$amount} ج.م مستحق في {$dueDate} ({$this->label}).";

        return [
            'installment_id' => $this->installment->id,
            'customer_id' => $this->installment->plan->customer_id ?? null,
            'customer_name' => $customerName,
            'amount' => $this->installment->amount,
            'due_date' => $dueDate,
            'days_left' => $this->daysLeft,
            'label' => $this->label,
            'title' => $title,
            'message' => $message,
            'url' => route('customers.show', $this->installment->plan->customer_id ?? 1),
        ];
    }
}
