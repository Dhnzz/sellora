<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\SalesAgent;
use App\Models\PurchaseOrderItem;
use App\Models\SalesTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'customer_id',
        'sales_agent_id',
        'order_date',
        'delivery_date',
        'status',
        'discount_percent',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function sales_agent(): BelongsTo
    {
        return $this->belongsTo(SalesAgent::class, 'sales_agent_id');
    }

    public function purchase_order_items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function sales_transaction(): HasOne
    {
        return $this->hasOne(SalesTransaction::class, 'sales_transaction_id');
    }
}
