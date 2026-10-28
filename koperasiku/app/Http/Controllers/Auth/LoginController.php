<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Akun nonaktif tidak boleh login.
        $attempt = Auth::attempt(
            $credentials + ['is_active' => true],
            $request->boolean('remember')
        );

        if (! $attempt) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
