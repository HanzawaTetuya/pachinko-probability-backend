<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
    ];
}
