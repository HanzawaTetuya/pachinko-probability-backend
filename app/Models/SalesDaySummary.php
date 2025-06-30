<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDaySummary extends Model
{
    use HasFactory;

    protected $table = 'sales_days_summaries';

    protected $fillable = [
        'date',
        'total_sales',
        'total_orders',
    ];
}

