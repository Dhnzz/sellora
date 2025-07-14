<?php

namespace App\Models;

use App\Models\Stock;
use App\Models\ProductUnit;
use App\Models\ProductBrand;
use App\Models\StockAdjustments;
use App\Models\ProductBundleItem;
use App\Models\ProductReturnItem;
use App\Models\PurchaseOrderItem;
use App\Models\SalesTransactionItem;
use Illuminate\Database\Eloquent\Model;
use App\Models\IncomingSupplyTransactionItem;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'product_brand_id',
        'name',
        'msu',
        'convertion_factors',
        'selling_price',
    ];

    public function product_brand(): BelongsTo
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    }

    public function product_unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'product_brand_id');
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'product_id');
    }

    public function purchase_order_items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'product_id');
    }

    public function sales_transaction_items(): HasMany
    {
        return $this->hasMany(SalesTransactionItem::class, 'product_id');
    }

    public function product_return_items(): HasMany
    {
        return $this->hasMany(ProductReturnItem::class, 'product_id');
    }

    public function incoming_supply_transaction_items(): HasMany
    {
        return $this->hasMany(IncomingSupplyTransactionItem::class, 'product_id');
    }

    public function stock_adjustments(): HasMany
    {
        return $this->hasMany(StockAdjustments::class, 'product_id');
    }
}
