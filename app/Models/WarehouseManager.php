<?php

namespace App\Models;

use App\Models\User;
use App\Models\IncomingSupplyTransaction;
use App\Models\StockAdjusments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseManager extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function incoming_supply_transactions(): HasMany
    {
        return $this->hasMany(IncomingSupplyTransaction::class, 'warehouse_manager_id');
    }

    public function stock_adjustments(): HasMany
    {
        return $this->hasMany(StockAdjusments::class, 'warehouse_manager_id');
    }
}
