<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalConfig extends Model
{
    protected $fillable = [
        'company_id',
        'type',
        'category_id',
        'min_amount',
        'max_amount',
        'requires_level_1',
        'requires_level_2',
        'requires_level_3',
        'level_1_role',
        'level_2_role',
        'level_3_role',
        'deadline_hours',
        'is_active',
        'approval_mode',
        'assigned_to',
        'effective_from',
        'effective_until',
        'skip_role',
        'level_2_min_amount',
        'level_3_min_amount',
    ];

    protected function casts(): array
    {
        return [
            'min_amount' => 'decimal:2',
            'max_amount' => 'decimal:2',
            'requires_level_1' => 'boolean',
            'requires_level_2' => 'boolean',
            'requires_level_3' => 'boolean',
            'is_active' => 'boolean',
            'deadline_hours' => 'integer',
            'approval_mode' => 'string',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'level_2_min_amount' => 'decimal:2',
            'level_3_min_amount' => 'decimal:2',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getLevelsAttribute(): array
    {
        $levels = [];
        if ($this->requires_level_1) $levels[] = 1;
        if ($this->requires_level_2) $levels[] = 2;
        if ($this->requires_level_3) $levels[] = 3;
        return $levels;
    }

    public function getRoleForLevel(int $level): ?string
    {
        return match ($level) {
            1 => $this->level_1_role,
            2 => $this->level_2_role,
            3 => $this->level_3_role,
            default => null,
        };
    }

    public function getActiveLevelsForAmount(float $amount): array
    {
        $levels = [];
        if ($this->requires_level_1) {
            $levels[] = 1;
        }
        if ($this->requires_level_2) {
            if ($this->level_2_min_amount === null || $amount >= $this->level_2_min_amount) {
                $levels[] = 2;
            }
        }
        if ($this->requires_level_3) {
            if ($this->level_3_min_amount === null || $amount >= $this->level_3_min_amount) {
                $levels[] = 3;
            }
        }
        return $levels;
    }
}
