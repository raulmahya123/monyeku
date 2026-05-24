<?php

namespace App\Console\Commands;

use App\Models\AuditTrail;
use Illuminate\Console\Command;

class PruneAuditTrails extends Command
{
    protected $signature = 'audit:prune {--days=90 : Hapus audit trail lebih dari N hari} {--dry-run : Hitung saja tanpa hapus}';
    protected $description = 'Hapus audit trail lama untuk menjaga performa database';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoff = now()->subDays($days);
        $dryRun = $this->option('dry-run');

        $query = AuditTrail::where('created_at', '<', $cutoff);
        $count = $query->count();

        if ($dryRun) {
            $this->info("[DRY RUN] Akan menghapus {$count} audit trail yang lebih dari {$days} hari.");
            return 0;
        }

        $query->delete();
        $this->info("Berhasil menghapus {$count} audit trail yang lebih dari {$days} hari.");
        return 0;
    }
}
