<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if (!$user->is_active) {
    Auth::logout();

    throw ValidationException::withMessages([
        'email' => 'Akun sedang dinonaktifkan. Silakan hubungi administrator.',
    ]);
}

        if ($user->role === 'dosen') {
            return redirect()
                ->route('dosen.dashboard')
                ->with(
                    'success',
                    'Login berhasil! Selamat datang, ' . $user->name . '.'
                );
        }

        if ($user->role === 'admin_kampus') {
            return redirect()
                ->route('admin-kampus.dashboard')
                ->with(
                    'success',
                    'Login berhasil! Selamat datang, ' . $user->name . '.'
                );
        }

        if ($user->role === 'admin_arah') {
            return redirect()
                ->route('admin-arah.dashboard')
                ->with(
                    'success',
                    'Login berhasil! Selamat datang, ' . $user->name . '.'
                );
        }

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                'Login berhasil! Selamat datang, ' . $user->name . '.'
            );
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}