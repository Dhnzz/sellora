<?php

namespace App\Models;

use App\Models\DeliveryReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryReturnItem extends Model
{
    protected $fillable  = [
        'delivery_return_id',
        'product_id',
        'quantity_returned',
    ];

    public function delivery_return(): BelongsTo
    {
        return $this->belongsTo(DeliveryReturn::class, 'delivery_return_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
