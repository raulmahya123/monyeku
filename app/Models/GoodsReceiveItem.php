<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceiveItem extends Model
{
    protected $table = 'goods_receive_items';

    protected $fillable = [
        'goods_receive_id',
        'purchase_order_item_id',
        'product_id',
        'quantity',
        'accepted_qty',
        'rejected_qty',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'accepted_qty' => 'decimal:2',
            'rejected_qty' => 'decimal:2',
        ];
    }

    public function goodsReceive(): BelongsTo
    {
        return $this->belongsTo(GoodsReceive::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
