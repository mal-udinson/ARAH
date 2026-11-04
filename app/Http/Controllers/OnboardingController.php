<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Isi kalau nama tabel program studi di databasemu tidak terdeteksi otomatis.
     * Contoh: 'program_studi'
     */
    private ?string $prodiTableOverride = null;

    public function show(Request $request): View|RedirectResponse
    {
        $m = $request->user()->mahasiswa;
        abort_if(! $m, 403, 'Akun ini belum terhubung dengan data mahasiswa.');

        // Sudah pernah mengisi: tidak muncul lagi
        if ($m->onboarded_at) {
            return redirect()->route('dashboard');
        }

        $kampus = Kampus::get()->sortBy('nama')->values();

        $p = $this->prodiInfo();
        $prodi = DB::table($p['table'])->get()->map(fn ($row) => [
            'id'        => $row->{$p['key']},
            'kampus_id' => $p['kampus'] ? $row->kampus_id : null,
            'nama'      => $p['name'] ? $row->{$p['name']} : '-',
        ])->sortBy('nama')->values();

        return view('onboarding.profil', compact('m', 'kampus', 'prodi'));
    }

    public function store(Request $request): RedirectResponse
    {
        $m = $request->user()->mahasiswa;
        abort_if(! $m, 403, 'Akun ini belum terhubung dengan data mahasiswa.');

        $p = $this->prodiInfo();

        $request->merge(['nim' => trim((string) $request->input('nim'))]);

        $data = $request->validate([
            'nim'           => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9]+$/', Rule::unique($m->getTable(), 'nim')->ignore($m->id)],
            'kampus_id'     => ['required', Rule::exists((new Kampus)->getTable(), 'id')],
            'prodi_id'      => ['required', Rule::exists($p['table'], $p['key'])->where(fn ($q) => $p['kampus'] ? $q->where('kampus_id', $request->input('kampus_id')) : $q)],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
        ], [
            'nim.required'           => 'NIM wajib diisi.',
            'nim.regex'              => 'NIM hanya boleh berisi huruf dan angka.',
            'nim.unique'             => 'NIM ini sudah terdaftar pada akun lain.',
            'kampus_id.required'     => 'Pilih kampusmu.',
            'kampus_id.exists'       => 'Kampus yang dipilih tidak valid.',
            'prodi_id.required'      => 'Pilih program studimu.',
            'prodi_id.exists'        => 'Program studi tidak sesuai dengan kampus yang dipilih.',
            'jenis_kelamin.required' => 'Pilih jenis kelamin.',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid.',
        ]);

        // forceFill: tidak perlu mengubah $fillable di model Mahasiswa
        $m->forceFill($data + ['onboarded_at' => now()])->save();

        return redirect()->route('dashboard')->with('success', 'Data dirimu sudah tersimpan. Selamat datang di ARAH!');
    }

    /**
     * Mencari tabel program studi di database, tanpa bergantung pada model.
     * Mengembalikan nama tabel, kolom kunci, kolom nama, dan apakah ada kolom kampus_id.
     */
    private function prodiInfo(): array
    {
        $candidates = [];

        if ($this->prodiTableOverride) {
            $candidates[] = $this->prodiTableOverride;
        }

        foreach (['App\\Models\\Prodi', 'App\\Models\\ProgramStudi'] as $class) {
            if (class_exists($class)) {
                $candidates[] = (new $class)->getTable();
            }
        }

        $candidates = array_merge($candidates, ['prodi', 'prodis', 'program_studi', 'program_studis', 'jurusan', 'jurusans']);

        foreach (array_unique($candidates) as $table) {
            if (Schema::hasTable($table)) {
                $cols = Schema::getColumnListing($table);

                return [
                    'table'  => $table,
                    'key'    => in_array('id', $cols, true) ? 'id' : $cols[0],
                    'name'   => collect(['nama', 'nama_prodi', 'nama_program_studi', 'name'])->first(fn ($c) => in_array($c, $cols, true)),
                    'kampus' => in_array('kampus_id', $cols, true),
                ];
            }
        }

        $semua = collect(Schema::getTables())->pluck('name')->implode(', ');

        abort(500, "Tabel program studi tidak ditemukan di database. Tabel yang ada: {$semua}. Isi nama tabelnya di properti \$prodiTableOverride pada OnboardingController.");
    }
}