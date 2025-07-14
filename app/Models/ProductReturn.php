<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\SalesTransaction;
use App\Models\ProductReturnItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReturn extends Model
{
    protected $fillable = ['customer_id', 'sales_transaction_id', 'return_date'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function sales_transaction(): BelongsTo
    {
        return $this->belongsTo(SalesTransaction::class, 'sales_transaction_id');
    }

    public function product_return_items(): HasMany
    {
        return $this->hasMany(ProductReturnItem::class, 'product_return_id');
    }
}
