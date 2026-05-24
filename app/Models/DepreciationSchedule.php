<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepreciationSchedule extends Model
{
    protected $table = 'depreciation_schedules';

    protected $fillable = [
        'fixed_asset_id',
        'period',
        'schedule_date',
        'depreciation_amount',
        'accumulated_depreciation',
        'book_value',
        'is_journalized',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
            'depreciation_amount' => 'decimal:2',
            'accumulated_depreciation' => 'decimal:2',
            'book_value' => 'decimal:2',
            'is_journalized' => 'boolean',
        ];
    }

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }
}
