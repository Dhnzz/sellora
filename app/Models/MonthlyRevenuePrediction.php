<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyRevenuePrediction extends Model
{
    protected $fillable = [
        'prediction_month',
        'predicted_revenue',
        'prediction_date',
    ];
}
