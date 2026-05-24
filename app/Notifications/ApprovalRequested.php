<?php

namespace App\Notifications;

use App\Models\Approval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApprovalRequested extends Notification
{
    use Queueable;

    public Approval $approval;

    public function __construct(Approval $approval)
    {
        $this->approval = $approval;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $approvable = $this->approval->approvable;

        $typeMap = [
            'App\Models\Transaction' => 'Transaksi',
            'App\Models\Invoice' => 'Invoice',
            'App\Models\Debt' => 'Hutang/Piutang',
            'App\Models\Budget' => 'Anggaran',
        ];

        $type = $typeMap[get_class($approvable)] ?? 'Item';

        $amount = $approvable?->amount ?? $approvable?->total ?? 0;

        return [
            'approval_id' => $this->approval->id,
            'approvable_type' => get_class($approvable),
            'approvable_id' => $approvable?->id,
            'level' => $this->approval->level,
            'message' => "Approval {$type} level {$this->approval->level} diperlukan: Rp " . number_format($amount, 0, ',', '.'),
            'url' => route('approvals.index'),
        ];
    }
}
