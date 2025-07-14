<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use App\Models\IncomingSupplyTransaction;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingSupplyTransactionItem extends Model
{
    protected $fillable = [
        'incoming_supply_transaction_id',
        'product_id',
        'quantity',
    ];

    public function incoming_supply_transaction(): BelongsTo
    {
        return $this->belongsTo(IncomingSupplyTransaction::class, 'incoming_supply_transaction_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
