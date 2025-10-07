<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\Tweet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
    public function show(User $user)
{
    // 🔽 Eager Loadingで必要なリレーションを定義
    $relationsToLoad = ['user', 'liked', 'bookmarkers'];

    if (auth()->user()->is($user)) {
        // 自分のプロフィールの場合
        $tweets = Tweet::query()
            // 🔽 Eager Loadingを追加 🔽
            ->with($relationsToLoad) 
            ->where('user_id', $user->id) // 自分のツイート
            ->orWhereIn('user_id', $user->follows->pluck('id')) // フォローしているユーザーのツイート
            ->latest()
            ->paginate(10);
    } else {
        // 他のユーザーのプロフィールの場合
        $tweets = $user
            ->tweets()
            // 🔽 Eager Loadingを追加 🔽
            ->with($relationsToLoad) 
            ->latest()
            ->paginate(10);
    }

    // ユーザーのフォロワーとフォローしているユーザーを取得 (ここはそのまま)
    $user->load(['follows', 'followers']);

    return view('profile.show', compact('user', 'tweets'));
}

}
