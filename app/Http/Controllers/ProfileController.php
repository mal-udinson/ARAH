<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profile / data diri.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::where(
            'user_id',
            $user->id
        )->first();

        return view('profile.edit', [
            'user' => $user,
            'mahasiswa' => $mahasiswa,
        ]);
    }


    /**
     * Menyimpan perubahan data diri.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Validasi data
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',

                Rule::unique('users', 'email')
                    ->ignore($user->id),
            ],

            'nim' => [
                'required',
                'string',
                'max:50',
            ],

            'semester' => [
                'required',
                'integer',
                'min:1',
                'max:14',
            ],

            'ipk' => [
                'required',
                'numeric',
                'min:0',
                'max:4',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Update tabel users
        |--------------------------------------------------------------------------
        */

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        /*
        | Kalau email berubah, status verifikasi email
        | dikembalikan menjadi belum terverifikasi.
        */

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();


        /*
        |--------------------------------------------------------------------------
        | Update / buat data mahasiswa
        |--------------------------------------------------------------------------
        */

        Mahasiswa::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'nim' => $validated['nim'],
                'nama' => $validated['name'],
                'semester' => $validated['semester'],
                'ipk' => $validated['ipk'],
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'status',
            'profile-updated'
        );
    }


    /**
     * Menghapus akun user.
     */
    public function destroy(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validasi password sebelum hapus akun
        |--------------------------------------------------------------------------
        */

        $request->validateWithBag(
            'userDeletion',
            [
                'password' => [
                    'required',
                    'current_password',
                ],
            ]
        );


        $user = $request->user();


        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        Auth::logout();


        /*
        |--------------------------------------------------------------------------
        | Hapus akun
        |--------------------------------------------------------------------------
        */

        $user->delete();


        /*
        |--------------------------------------------------------------------------
        | Hapus session
        |--------------------------------------------------------------------------
        */

        $request->session()->invalidate();

        $request->session()->regenerateToken();


        return redirect('/');
    }
}