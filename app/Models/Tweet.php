<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    /** @use HasFactory<\Database\Factories\TweetFactory> */
    use HasFactory;
    protected $fillable = ['tweet'];

    // 🔽 1対多の関係
    public function comments()
    {
    return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }
    
    public function user()
    {
    return $this->belongsTo(User::class);
    }
    public function liked()
    {
      return $this->belongsToMany(User::class)->withTimestamps();
    }
    // 🔽 ブックマーク機能用に追加
    public function bookmarkers()
    {
    // Userモデルへのリレーションを定義し、中間テーブル名 'bookmarks' を明示的に指定
    return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
    }

}
