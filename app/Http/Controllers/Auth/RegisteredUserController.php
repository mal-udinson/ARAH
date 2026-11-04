<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        /*
         * Buat user + data mahasiswa dalam satu transaksi.
         * Kalau salah satu gagal, semuanya dibatalkan.
         */
        $user = DB::transaction(function () use ($validated) {

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            Mahasiswa::create([
                'user_id' => $user->id,
                'nim' => 'TEMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                'nama' => $validated['name'],
                'semester' => 1,
                'ipk' => 0,
            ]);

            return $user;
        });

        event(new Registered($user));

        // Tidak auto-login: arahkan ke halaman login dulu.
        return redirect()->route('login')
            ->with('status', 'Akun berhasil dibuat! Silakan masuk.');
    }
}