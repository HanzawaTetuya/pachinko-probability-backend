<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'question', 'answer', 'order_index'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
