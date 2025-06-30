<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesMonthSummary extends Model
{
    use HasFactory;

    protected $table = 'sales_month_summaries';

    protected $fillable = [
        'year',
        'month',
        'total_sales',
        'total_orders',
    ];
}