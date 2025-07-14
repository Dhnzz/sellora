<?php

namespace App\Models;

use App\Models\Supplier;
use App\Models\WarehouseManager;
use App\Models\IncomingSupplyTransactionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingSupplyTransaction extends Model
{
    protected $fillable = [
        'warehouse_manager_id',
        'supplier_id',
        'transaction_date',
    ];

    public function warehouse_manager(): BelongsTo
    {
        return $this->belongsTo(WarehouseManager::class, 'warehouse_manager_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function incoming_supply_transaction_items(): HasMany
    {
        return $this->hasMany(IncomingSupplyTransactionItem::class, 'incoming_supply_transaction_id');
    }
}
