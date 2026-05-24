<?php

namespace App\Services;

use App\Models\Approval;
use App\Models\ApprovalConfig;
use App\Models\StockOpname;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\ApprovalRequested;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ApprovalService
{
    public function getConfig(string $type, int $companyId, ?int $categoryId, float $amount): ?ApprovalConfig
    {
        return ApprovalConfig::where('company_id', $companyId)
            ->where('type', $type)
            ->where('is_active', true)
            ->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereNull('category_id');
            })
            ->where('min_amount', '<=', $amount)
            ->where(function ($q) use ($amount) {
                $q->whereNull('max_amount')
                  ->orWhere('max_amount', '>=', $amount);
            })
            ->where(function ($q) {
                $q->whereNull('effective_from')
                  ->orWhere('effective_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', now());
            })
            ->orderByRaw('CASE WHEN category_id IS NOT NULL THEN 0 ELSE 1 END')
            ->first();
    }

    public function requiresApproval(?ApprovalConfig $config): bool
    {
        return $config && $config->requires_level_1;
    }

    public function shouldSkipApproval(?ApprovalConfig $config, User $user, int $companyId): bool
    {
        if ($config === null) {
            return false;
        }

        if ($config->skip_role) {
            $pivot = $user->companies->where('id', $companyId)->first()?->pivot;
            $pivotRole = $pivot?->role ?? $user->role;

            if ($pivotRole === $config->skip_role) {
                return true;
            }
        }

        return false;
    }

    public function getEffectiveLevels(int $companyId, ApprovalConfig $config, float $amount): array
    {
        $levels = $config->levels;

        if ($config->level_2_min_amount !== null && $amount < $config->level_2_min_amount) {
            $levels = array_values(array_filter($levels, fn ($level) => $level !== 2));
        }

        if ($config->level_3_min_amount !== null && $amount < $config->level_3_min_amount) {
            $levels = array_values(array_filter($levels, fn ($level) => $level !== 3));
        }

        return $levels;
    }

    public function createApprovals(Model $approvable, ApprovalConfig $config, int $companyId, float $amount): void
    {
        $users = User::whereHas('companies', function ($q) use ($companyId) {
            $q->where('company_id', $companyId)->where('is_active', true);
        })->get();

        $deadlineHours = $config->deadline_hours;
        $deadlineAt = $deadlineHours ? now()->addHours($deadlineHours) : null;

        $levels = $this->getEffectiveLevels($companyId, $config, $amount);

        foreach ($levels as $level) {
            $role = $config->getRoleForLevel($level);

            $levelApprovers = $users->filter(function ($user) use ($role, $companyId) {
                $pivot = $user->companies->where('id', $companyId)->first()?->pivot;
                $pivotRole = $pivot?->role ?? $user->role;
                return $pivotRole === $role;
            });

            if ($config->assigned_to) {
                $assignedUser = $users->firstWhere('id', $config->assigned_to);

                if ($assignedUser) {
                    $approval = Approval::create([
                        'approvable_id' => $approvable->id,
                        'approvable_type' => get_class($approvable),
                        'approver_id' => $assignedUser->id,
                        'level' => $level,
                        'status' => 'pending',
                        'deadline_at' => $deadlineAt,
                    ]);

                    Notification::send($assignedUser, new ApprovalRequested($approval));
                }

                continue;
            }

            foreach ($levelApprovers as $approver) {
                $approval = Approval::create([
                    'approvable_id' => $approvable->id,
                    'approvable_type' => get_class($approvable),
                    'approver_id' => $approver->id,
                    'level' => $level,
                    'status' => 'pending',
                    'deadline_at' => $deadlineAt,
                ]);

                Notification::send($approver, new ApprovalRequested($approval));
            }
        }
    }

    public function autoRejectExpired(): int
    {
        $expired = Approval::where('status', 'pending')
            ->whereNotNull('deadline_at')
            ->where('deadline_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expired->groupBy(['approvable_type', 'approvable_id']) as $type => $items) {
            foreach ($items as $approvableId => $approvals) {
                $approvable = null;
                try {
                    $approvable = $type::find($approvableId);
                } catch (\Throwable $e) {
                    continue;
                }

                if (!$approvable) continue;

                $approvals->each->update([
                    'status' => 'rejected',
                    'notes' => 'Otomatis ditolak — melewati batas waktu.',
                    'approved_at' => now(),
                ]);

                $updateData = ['status' => 'rejected'];
                if (!($approvable instanceof Transaction)) {
                    $updateData['approval_status'] = 'rejected';
                }
                $approvable->update($updateData);

                $count++;
            }
        }

        return $count;
    }
}
