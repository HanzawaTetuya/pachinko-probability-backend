<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    // テーブル名
    protected $table = 'results';

    // 複数代入を許可する属性
    protected $fillable = [
        'result_number',
        'machine_number',
        'product_name',
        'hit_probability',
        'expected_chain_count',
        'chain_probability',
        'current_bonus',
        'cash_balance_3_3',
    ];

    // 属性キャスト
    protected $casts = [
        'hit_probability' => 'float',
        'expected_chain_count' => 'float',
        'chain_probability' => 'float',
        'current_bonus' => 'string',
        'cash_balance_3_3' => 'string',
    ];

    // リレーション: Result は ResultUsage を持つ
    public function usages()
    {
        return $this->hasMany(ResultUsage::class, 'result_number', 'result_number');
    }
}
