<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\Auditable;

class Budget extends Model
{
    use Auditable;
    protected $fillable = [
        'company_id',
        'category_id',
        'period',
        'amount',
        'spent',
        'month',
        'year',
        'notification_threshold',
        'approval_status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'spent' => 'decimal:2',
            'notification_threshold' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
            'approved_at' => 'datetime',
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

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public function getRemainingAttribute(): float
    {
        return $this->amount - $this->spent;
    }

    public function getPercentageAttribute(): float
    {
        if ($this->amount == 0) return 0;
        return round(($this->spent / $this->amount) * 100, 1);
    }
}
