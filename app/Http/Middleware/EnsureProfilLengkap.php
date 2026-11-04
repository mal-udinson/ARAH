<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfilLengkap
{
    /** Mahasiswa yang belum mengisi data diri dialihkan ke form pengisian. */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'mahasiswa') {
            $m = $user->mahasiswa;

            if ($m && empty($m->onboarded_at)) {
                return redirect()->route('onboarding.show');
            }
        }

        return $next($request);
    }
}