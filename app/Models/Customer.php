<?php

namespace App\Models;

use App\Models\User;
use App\Models\SalesAgent;
use App\Models\PurchaseOrder;
use App\Models\ProductReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'sales_agent_id',
        'name',
        'phone',
        'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sales_agent(): BelongsTo
    {
        return $this->belongsTo(SalesAgent::class, 'sales_agent_id');
    }

    public function purchase_orders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'customer_id');
    }

    public function product_returns(): BelongsTo
    {
        return $this->hasMany(ProductReturn::class, 'customer_id');
    }
}
