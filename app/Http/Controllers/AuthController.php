<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $key = Str::transliterate(Str::lower($request->email).'|'.$request->ip());
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.'])->onlyInput('email');
        } if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['email' => 'Email atau password tidak cocok.'])->onlyInput('email');
        } RateLimiter::clear($key);
        if (Auth::user()->status !== 'active') {
            Auth::logout();

            return back()->withErrors(['email' => 'Akun Anda tidak aktif.']);
        } $request->session()->regenerate();

        return redirect()->intended(route(Auth::user()->role.'.dashboard'));
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:120', 'email' => 'required|email|max:255|unique:users,email', 'phone' => 'nullable|string|max:30', 'password' => 'required|string|min:8|confirmed']);
        $data['role'] = 'participant';
        $data['status'] = 'active';
        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('participant.dashboard')->with('success', 'Akun peserta berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
