<?php

namespace App\Models;

use App\Models\Admin;
use App\Models\ProductReturn;
use App\Models\PurchaseOrder;
use App\Models\SalesTransactionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTransaction extends Model
{
    protected $fillable = ['purchase_order_id', 'admin_id', 'invoice_date', 'discount_percent', 'total_amount', 'payment_status', 'delivery_confirmed_at'];

    public function purchase_order(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function sales_transaction_items(): HasMany
    {
        return $this->hasMany(SalesTransactionItem::class, 'sales_transaction_id');
    }

    public function product_returns(): HasMany
    {
        return $this->hasMany(ProductReturn::class, 'sales_transaction_id');
    }
}
