<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coa extends Model
{
    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'company_id', 'code', 'name', 'type', 'normal_balance',
        'parent_id', 'description', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getBalanceAttribute()
    {
        $debit = $this->journalLines()->sum('debit');
        $credit = $this->journalLines()->sum('credit');
        if ($this->normal_balance === 'debit') {
            return $debit - $credit;
        }
        return $credit - $debit;
    }

    public function journalLines()
    {
        return $this->hasMany(JournalLine::class, 'coa_id');
    }
}
