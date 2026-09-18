<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleSocialiteUser;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    public function create(): InertiaResponse
    {
        return Inertia::render('Auth/Login');
    }

    public function redirectToGoogle(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('login')->withErrors(['email' => 'Google sign-in was cancelled.']);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('login')->withErrors(['email' => 'Google sign-in could not be completed.']);
        }

        if (! $googleUser instanceof GoogleSocialiteUser) {
            return redirect()->route('login')->withErrors(['email' => 'Google sign-in could not be completed.']);
        }

        $email = strtolower(trim((string) $googleUser->getEmail()));
        $rawProfile = $googleUser->user;
        $isVerified = filter_var($rawProfile['email_verified'] ?? $rawProfile['verified_email'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if ($email === '' || ! $isVerified) {
            return redirect()->route('login')->withErrors(['email' => 'This Google account is not authorized.']);
        }

        $user = User::query()->where('email', $email)->first();
        $isUnlinkedOrEmail = $user !== null && ($user->google_id === null || $user->google_id === $email);

        if ($user === null || (! $isUnlinkedOrEmail && $user->google_id !== $googleUser->getId())) {
            return redirect()->route('login')->withErrors(['email' => 'This Google account is not authorized.']);
        }

        $linkedUser = User::query()->where('google_id', $googleUser->getId())->first();

        if ($linkedUser !== null && $linkedUser->isNot($user)) {
            return redirect()->route('login')->withErrors(['email' => 'This Google account is not authorized.']);
        }

        $user->forceFill(['google_id' => $googleUser->getId()])->save();
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('devices.index'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
