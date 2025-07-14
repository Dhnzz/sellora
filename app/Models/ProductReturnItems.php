<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReturnItems extends Model
{
    protected $fillable = [
        'product_return_id',
        'product_id',
        'quantity',
    ];

    public function product_return(): BelongsTo
    {
        return $this->belongsTo(ProductReturn::class, 'product_return_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
