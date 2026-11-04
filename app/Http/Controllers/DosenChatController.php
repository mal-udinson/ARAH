<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\DosenChatMessage;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class DosenChatController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MAHASISWA
    |--------------------------------------------------------------------------
    */

    public function studentIndex(Request $request)
    {
        $user = $request->user();

        $mahasiswa = Mahasiswa::with([
            'dosenPa',
            'dosenPa.user',
            'dosenPa.kampus',
        ])
            ->where('user_id', $user->id)
            ->firstOrFail();

        $dosen = $mahasiswa->dosenPa;

        if (!$dosen) {
            return view('chat-dosen', [
                'mahasiswa' => $mahasiswa,
                'dosen' => null,
                'messages' => collect(),
            ]);
        }

        $messages = DosenChatMessage::with('sender')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('dosen_id', $dosen->id)
            ->orderBy('created_at')
            ->get();

        DosenChatMessage::where('mahasiswa_id', $mahasiswa->id)
            ->where('dosen_id', $dosen->id)
            ->where('sender_user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return view('chat-dosen', compact(
            'mahasiswa',
            'dosen',
            'messages'
        ));
    }

    public function studentSend(Request $request)
    {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $user = $request->user();

        $mahasiswa = Mahasiswa::where('user_id', $user->id)
            ->firstOrFail();

        $dosen = $mahasiswa->dosenPa;

        if (!$dosen) {
            return back()->with(
                'error',
                'Anda belum memiliki Dosen PA.'
            );
        }

        DosenChatMessage::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'sender_user_id' => $user->id,
            'message' => trim($request->message),
        ]);

        return redirect()
            ->route('chat-dosen')
            ->with('success', 'Pesan berhasil dikirim.');
    }


    /*
    |--------------------------------------------------------------------------
    | DOSEN
    |--------------------------------------------------------------------------
    */

    public function dosenIndex(Request $request)
    {
        $dosen = Dosen::with('kampus')
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $mahasiswa = $dosen->mahasiswa()
            ->with('user')
            ->orderBy('nama')
            ->get();

        return view('dosen.chat.index', compact(
            'dosen',
            'mahasiswa'
        ));
    }

    public function dosenChat(
        Request $request,
        int $mahasiswaId
    ) {
        $user = $request->user();

        $dosen = Dosen::where('user_id', $user->id)
            ->firstOrFail();

        /*
         * Ini bagian penting:
         * dosen hanya bisa membuka mahasiswa yang memang
         * terdaftar sebagai mahasiswa bimbingannya.
         */
        $mahasiswa = $dosen->mahasiswa()
            ->with('user')
            ->whereKey($mahasiswaId)
            ->firstOrFail();

        $messages = DosenChatMessage::with('sender')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('dosen_id', $dosen->id)
            ->orderBy('created_at')
            ->get();

        DosenChatMessage::where('mahasiswa_id', $mahasiswa->id)
            ->where('dosen_id', $dosen->id)
            ->where('sender_user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        return view('dosen.chat.show', compact(
            'dosen',
            'mahasiswa',
            'messages'
        ));
    }

    public function dosenSend(
        Request $request,
        int $mahasiswaId
    ) {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $user = $request->user();

        $dosen = Dosen::where('user_id', $user->id)
            ->firstOrFail();

        /*
         * Sekali lagi, validasi bahwa mahasiswa ini
         * memang mahasiswa bimbingan dosen tersebut.
         */
        $mahasiswa = $dosen->mahasiswa()
            ->whereKey($mahasiswaId)
            ->firstOrFail();

        DosenChatMessage::create([
            'mahasiswa_id' => $mahasiswa->id,
            'dosen_id' => $dosen->id,
            'sender_user_id' => $user->id,
            'message' => trim($request->message),
        ]);

        return redirect()
            ->route(
                'dosen.chat.show',
                $mahasiswa->id
            )
            ->with('success', 'Pesan berhasil dikirim.');
    }
}