<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomMaterial extends Model
{
    protected $table = 'bom_materials';

    protected $fillable = [
        'bom_id',
        'material_id',
        'quantity',
        'waste_percentage',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'waste_percentage' => 'decimal:2',
        ];
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'material_id');
    }
}
