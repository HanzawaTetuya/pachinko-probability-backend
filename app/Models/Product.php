<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // どのフィールドに対して外部からの入力を許可するか
    protected $fillable = [
        'name',
        'manufacturer',
        'category',
        'price',
        'release_date',
        'description',
        'image_path',
        'python_file_path',
        'is_published',
        'product_number'
    ];

    // 重要なフィールドを隠す。例えば、Pythonファイルのパスは外部に見せない。
    protected $hidden = [
        'python_file_path',  // Pythonファイルのパスは見せない
    ];

    // フィールドを適切な型にキャストする
    protected $casts = [
        'price' => 'decimal:2',  // 金額は小数点以下2桁
        'release_date' => 'date',  // 発売日は日付型
        'is_published' => 'boolean',  // 公開設定はブール型
        'created_at' => 'datetime',  // 作成日時を日時型にキャスト
        'updated_at' => 'datetime',  // 更新日時を日時型にキャスト
    ];

    /**
     * 公開されている商品だけを取得するスコープ
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * 画像ファイルのURLを取得するアクセサ
     */
    public function getImageUrlAttribute()
    {
        // nullの場合はデフォルト画像を返す
        return $this->image_path ? asset('storage/' . $this->image_path) : asset('images/default-product.png');
    }

    /**
     * Pythonファイルのアクセス方法 (外部に渡さない)
     * 必要に応じて内部でのみ使用
     */
    public function getPythonFile()
    {
        // 正しい保存パスを指定
        return storage_path('app/private/' . $this->python_file_path);
    }
    

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $lastProduct = Product::orderBy('product_number', 'desc')->first();
            $product->product_number = $lastProduct ? $lastProduct->product_number + 1 : 100000;
        });
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'product_number', 'product_number');
    }
}
