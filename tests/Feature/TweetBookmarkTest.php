<?php

use App\Models\User;
use App\Models\Tweet;

it('has tweetbookmark page', function () {
    // 認証されていないとリダイレクトされるため、ユーザーを作成し actingAs でアクセスします
    $user = User::factory()->create();
    
    // ログインユーザーとして、ブックマーク一覧のURIにアクセス
    $response = $this->actingAs($user)->get('/bookmarks'); 

    $response->assertStatus(200); // 成功ステータスを確認
});

// ブックマーク追加のテスト
it('allows a user to bookmark a tweet', function () {
  $user = User::factory()->create();
  $tweet = Tweet::factory()->create();

  $this->actingAs($user)
    // 🔽 ルート名とURIをブックマーク用に変更 🔽
    ->post(route('tweets.bookmark', ['tweet' => $tweet->id])) 
    ->assertStatus(302); // リダイレクト（back()）を確認

  // 🔽 中間テーブル名とデータを確認 🔽
  $this->assertDatabaseHas('bookmarks', [
    'user_id' => $user->id,
    'tweet_id' => $tweet->id
  ]);
});

// ブックマーク解除のテスト
it('allows a user to unbookmark a tweet', function () {
  $user = User::factory()->create();
  $tweet = Tweet::factory()->create();

  // 🔽 最初にリレーションメソッドを使ってブックマークする 🔽
  // Userモデルの 'bookmarks' リレーションを使用
  $user->bookmarks()->attach($tweet); 

  $this->actingAs($user)
    // 🔽 ルート名とURIをブックマーク解除用に変更 🔽
    ->delete(route('tweets.unbookmark', ['tweet' => $tweet->id]))
    ->assertStatus(302); // リダイレクト（back()）を確認

  // 🔽 中間テーブルにレコードが存在しないことを確認 🔽
  $this->assertDatabaseMissing('bookmarks', [
    'user_id' => $user->id,
    'tweet_id' => $tweet->id
  ]);
});
