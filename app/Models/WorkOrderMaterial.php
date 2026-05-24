<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrderMaterial extends Model
{
    protected $table = 'work_order_materials';

    protected $fillable = [
        'work_order_id',
        'product_id',
        'required_qty',
        'used_qty',
    ];

    protected function casts(): array
    {
        return [
            'required_qty' => 'decimal:2',
            'used_qty' => 'decimal:2',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
