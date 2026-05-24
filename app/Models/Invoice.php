<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\Auditable;

class Invoice extends Model
{
    use Auditable;
    protected $fillable = [
        'company_id',
        'user_id',
        'invoice_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'items',
        'subtotal',
        'tax',
        'total',
        'status',
        'approval_status',
        'approved_at',
        'issue_date',
        'due_date',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'issue_date' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvals(): MorphMany
    {
        return $this->morphMany(Approval::class, 'approvable');
    }

    public static function generateNumber($companyId): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        $last = static::where('company_id', $companyId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        return sprintf('%s/%s%s/%04d', $prefix, $year, $month, $last + 1);
    }
}
