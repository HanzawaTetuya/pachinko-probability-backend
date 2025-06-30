<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultUsage extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'results_usage';

    // 複数代入を許可する属性
    protected $fillable = [
        'user_id',
        'result_number',
        'usage_date',
        'usage_count',
    ];

    // 属性キャスト
    protected $casts = [
        'usage_date' => 'date',
        'usage_count' => 'integer',
    ];

    // リレーション: ResultUsage は Result に属する
    public function result()
    {
        return $this->belongsTo(Result::class, 'result_number', 'result_number');
    }
}
