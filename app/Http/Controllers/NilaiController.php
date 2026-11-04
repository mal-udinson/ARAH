<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $mahasiswa = Mahasiswa::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $mataKuliah = MataKuliah::orderBy('kode')->get();

        $nilai = Nilai::with('mataKuliah')
            ->where('mahasiswa_id', $mahasiswa->id)
            ->orderBy('mata_kuliah_id')
            ->get();

        $rataNilai = $nilai->avg('nilai_angka');

        return view('academic.index', compact(
            'mahasiswa',
            'mataKuliah',
            'nilai',
            'rataNilai'
        ));
    }

    public function store(Request $request)
    {
        $mahasiswa = Mahasiswa::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        $validated = $request->validate([
            'mata_kuliah_id' => [
                'required',
                'exists:mata_kuliah,id',
            ],
            'nilai_angka' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            'nilai_huruf' => [
                'required',
                'string',
                'max:5',
            ],
        ]);

        Nilai::updateOrCreate(
            [
                'mahasiswa_id' => $mahasiswa->id,
                'mata_kuliah_id' => $validated['mata_kuliah_id'],
            ],
            [
                'nilai_angka' => $validated['nilai_angka'],
                'nilai_huruf' => strtoupper(
                    $validated['nilai_huruf']
                ),
            ]
        );

        return back()->with(
            'success',
            'Nilai berhasil disimpan.'
        );
    }

    public function destroy(
        Request $request,
        Nilai $nilai
    ) {
        $mahasiswa = Mahasiswa::where(
            'user_id',
            $request->user()->id
        )->firstOrFail();

        abort_unless(
            $nilai->mahasiswa_id === $mahasiswa->id,
            403
        );

        $nilai->delete();

        return back()->with(
            'success',
            'Nilai berhasil dihapus.'
        );
    }
}