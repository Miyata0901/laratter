<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * ログインユーザーのブックマーク一覧を表示する
     */
    public function index()
    {
        // 認証ユーザーの 'bookmarks' リレーション経由でツイートを取得
        // latest() で新しい順に並べ、paginate() でページネーションを適用
        $bookmarkedTweets = auth()->user()
                                  ->bookmarks()
                                  ->latest()
                                  ->paginate(20);

        // ブックマーク一覧用のビューにデータを渡す
        return view('bookmarks.index', [
            'tweets' => $bookmarkedTweets
        ]);
    }
}