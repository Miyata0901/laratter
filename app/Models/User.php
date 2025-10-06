<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function tweets() //tweetsメソッド
    {
    return $this->hasMany(Tweet::class);
    }

    public function comments()
    {
    return $this->hasMany(Comment::class);
    }
    
    public function likes()
    {
      return $this->belongsToMany(Tweet::class)->withTimestamps();
    }
    public function follows()
    {
        return $this->belongsToMany(User::class, 'follows', 'follow_id', 'follower_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'follow_id');
    }

    // 🔽 ブックマーク機能用に追加
    public function bookmarks()
    {
    // belongsToMany の第二引数に、作成した中間テーブル名 'bookmarks' を指定
    // これにより、Tweetモデルとの多対多リレーションを 'bookmarks' テーブル経由で行う
        return $this->belongsToMany(Tweet::class, 'bookmarks')->withTimestamps();
    }
}
