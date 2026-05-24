<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedAsset extends Model
{
    use Auditable;

    protected $table = 'fixed_assets';

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'category_id',
        'purchase_date',
        'purchase_price',
        'residual_value',
        'useful_life',
        'depreciation_method',
        'accumulated_depreciation',
        'book_value',
        'depreciation_start_date',
        'location',
        'description',
        'status',
        'disposal_date',
        'disposal_value',
        'coa_id',
        'depreciation_coa_id',
        'accumulation_coa_id',
        'branch_id',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'purchase_price' => 'decimal:2',
            'residual_value' => 'decimal:2',
            'useful_life' => 'integer',
            'accumulated_depreciation' => 'decimal:2',
            'book_value' => 'decimal:2',
            'depreciation_start_date' => 'date',
            'disposal_date' => 'date',
            'disposal_value' => 'decimal:2',
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

    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class);
    }

    public function depreciationCoa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'depreciation_coa_id');
    }

    public function accumulationCoa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'accumulation_coa_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function depreciationSchedules(): HasMany
    {
        return $this->hasMany(DepreciationSchedule::class);
    }

    public function calculateMonthlyDepreciation(): float
    {
        $depreciable = $this->purchase_price - $this->residual_value;
        if ($depreciable <= 0 || $this->useful_life <= 0) return 0;
        $months = $this->useful_life;
        return match ($this->depreciation_method) {
            'straight_line' => $depreciable / $months,
            'double_declining' => ($this->book_value > 0 ? $this->book_value : $this->purchase_price) * (2 / $months),
            'sum_of_years' => $depreciable * (($months - $this->depreciationSchedules()->count()) / ($months * ($months + 1) / 2)),
            default => $depreciable / $months,
        };
    }
}
