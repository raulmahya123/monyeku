<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Approval extends Model
{
    protected $fillable = [
        'approvable_id',
        'approvable_type',
        'approver_id',
        'level',
        'status',
        'notes',
        'approved_at',
        'notified_at',
        'deadline_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'notified_at' => 'datetime',
            'deadline_at' => 'datetime',
        ];
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
