<?php

namespace App\Console\Commands;

use App\Services\ApprovalService;
use Illuminate\Console\Command;

class ApprovalsAutoReject extends Command
{
    protected $signature = 'approvals:auto-reject';
    protected $description = 'Auto-reject approvals that have passed their deadline';

    public function handle(ApprovalService $approvalService)
    {
        $count = $approvalService->autoRejectExpired();

        $this->info("{$count} approval(s) auto-rejected due to deadline.");
    }
}
