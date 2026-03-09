<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda tidak aktif. Hubungi administrator.']);
            }

            $request->session()->regenerate();

            if ($remember) {
                config(['session.lifetime' => 43200]); // 30 days
            }

            activity()
                ->causedBy($user)
                ->withProperties(['ip' => $request->ip()])
                ->log('User login');

            return redirect()->intended('/dashboard');
        }

        activity()
            ->withProperties(['email' => $request->email, 'ip' => $request->ip()])
            ->log('Failed login attempt');

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        activity()
            ->causedBy($user)
            ->log('User logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
