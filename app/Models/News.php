<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    /**
     * テーブル名の指定
     *
     * @var string
     */
    protected $table = 'news';

    /**
     * ホワイトリスト（mass assignment を許可するカラム）
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'image_path',
        'published_at',
    ];

    /**
     * キャスト（データ型の変換）
     *
     * @var array
     */
    protected $casts = [
        'published_at' => 'datetime',
    ];
    
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'news_tags', 'news_id', 'tag_id');
    }
}
