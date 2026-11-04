<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\NaraChatController;
use App\Http\Controllers\NaraVoiceController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DosenChatController;
use App\Http\Controllers\AdminKampusController;
use App\Http\Controllers\AdminKampusMahasiswaController;
use App\Http\Controllers\AdminKampusDosenController;
use App\Http\Controllers\AdminArahController;
use App\Http\Controllers\AdminArahKampusController;
use App\Http\Controllers\AdminArahAdminKampusController;
use App\Http\Controllers\AdminArahMahasiswaController;
use App\Http\Controllers\AdminArahDosenController;
use App\Http\Controllers\AdminArahMonitoringController;
use App\Http\Controllers\AdminArahAuditLogController;
use App\Http\Middleware\EnsureProfilLengkap;

/*
|--------------------------------------------------------------------------
| HALAMAN UTAMA: arahkan ke dashboard sesuai peran
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return redirect()->route(match (auth()->user()->role) {
        'dosen'        => 'dosen.dashboard',
        'admin_kampus' => 'admin-kampus.dashboard',
        'admin_arah'   => 'admin-arah.dashboard',
        default        => 'dashboard',
    });
});

/*
|--------------------------------------------------------------------------
| ROUTE YANG MEMBUTUHKAN LOGIN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /* ------------- MAHASISWA: form data diri (pertama kali login) ------------- */
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/lengkapi-data', [OnboardingController::class, 'show'])->name('onboarding.show');
        Route::post('/lengkapi-data', [OnboardingController::class, 'store'])->name('onboarding.store');
    });

    /* ------------- MAHASISWA: wajib sudah mengisi data diri ------------- */
    Route::middleware(['role:mahasiswa', EnsureProfilLengkap::class])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Asesmen dan profil minat
        Route::get('/asesmen', [AssessmentController::class, 'show'])->name('assessment.show');
        Route::post('/asesmen', [AssessmentController::class, 'store'])->name('assessment.store');
        Route::get('/profil-minat', [AssessmentController::class, 'profile'])->name('interest.profile');

        // Rekomendasi
        Route::get('/rekomendasi', [RecommendationController::class, 'index'])->name('recommendations');
        Route::get('/rekomendasi/generate', [RecommendationController::class, 'generate'])->name('recommendations.generate');

        // Data akademik
        Route::get('/data-akademik', [NilaiController::class, 'index'])->name('academic.index');
        Route::post('/data-akademik', [NilaiController::class, 'store'])->name('academic.store');
        Route::delete('/data-akademik/{nilai}', [NilaiController::class, 'destroy'])->name('academic.destroy');

        // Profil
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // NARA (chat dengan riwayat)
        Route::get('/chat', [NaraChatController::class, 'index'])->name('chat');
        Route::get('/chat/{id}', [NaraChatController::class, 'index'])->whereNumber('id')->name('chat.show');
        Route::post('/chat/send', [NaraChatController::class, 'send'])->name('chat.send');
        Route::patch('/chat/{id}', [NaraChatController::class, 'rename'])->whereNumber('id')->name('chat.rename');
        Route::delete('/chat/{id}', [NaraChatController::class, 'destroy'])->whereNumber('id')->name('chat.destroy');

        // NARA voice
        Route::get('/nara/voice', [NaraVoiceController::class, 'index'])->name('nara.voice');
        Route::post('/nara/voice/session', [NaraVoiceController::class, 'session'])->name('nara.voice.session');

        // Chat dengan dosen PA
        Route::get('/chat-dosen', [DosenChatController::class, 'studentIndex'])->name('chat-dosen');
        Route::post('/chat-dosen/send', [DosenChatController::class, 'studentSend'])->name('chat-dosen.send');
    });

    /* ----------------------------- DOSEN PA ----------------------------- */
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {

        Route::get('/dashboard', [DosenController::class, 'index'])->name('dashboard');
        Route::get('/mahasiswa/{mahasiswaId}', [DosenController::class, 'showMahasiswa'])->whereNumber('mahasiswaId')->name('mahasiswa.show');

        // Chat dengan mahasiswa
        Route::get('/chat', [DosenChatController::class, 'dosenIndex'])->name('chat.index');
        Route::get('/chat/{mahasiswaId}', [DosenChatController::class, 'dosenChat'])->whereNumber('mahasiswaId')->name('chat.show');
        Route::post('/chat/{mahasiswaId}', [DosenChatController::class, 'dosenSend'])->whereNumber('mahasiswaId')->name('chat.send');
    });

    /* ---------------------------- ADMIN KAMPUS ---------------------------- */
    Route::middleware(['role:admin_kampus', 'audit'])->prefix('admin-kampus')->name('admin-kampus.')->group(function () {

        Route::get('/dashboard', [AdminKampusController::class, 'index'])->name('dashboard');

        // CRUD mahasiswa
        Route::get('/mahasiswa', [AdminKampusMahasiswaController::class, 'index'])->name('mahasiswa.index');
        Route::get('/mahasiswa/create', [AdminKampusMahasiswaController::class, 'create'])->name('mahasiswa.create');
        Route::post('/mahasiswa', [AdminKampusMahasiswaController::class, 'store'])->name('mahasiswa.store');
        Route::get('/mahasiswa/{mahasiswaId}/edit', [AdminKampusMahasiswaController::class, 'edit'])->whereNumber('mahasiswaId')->name('mahasiswa.edit');
        Route::put('/mahasiswa/{mahasiswaId}', [AdminKampusMahasiswaController::class, 'update'])->whereNumber('mahasiswaId')->name('mahasiswa.update');
        Route::delete('/mahasiswa/{mahasiswaId}', [AdminKampusMahasiswaController::class, 'destroy'])->whereNumber('mahasiswaId')->name('mahasiswa.destroy');

        // CRUD dosen
        Route::get('/dosen', [AdminKampusDosenController::class, 'index'])->name('dosen.index');
        Route::get('/dosen/create', [AdminKampusDosenController::class, 'create'])->name('dosen.create');
        Route::post('/dosen', [AdminKampusDosenController::class, 'store'])->name('dosen.store');
        Route::get('/dosen/{dosenId}/edit', [AdminKampusDosenController::class, 'edit'])->whereNumber('dosenId')->name('dosen.edit');
        Route::put('/dosen/{dosenId}', [AdminKampusDosenController::class, 'update'])->whereNumber('dosenId')->name('dosen.update');
        Route::delete('/dosen/{dosenId}', [AdminKampusDosenController::class, 'destroy'])->whereNumber('dosenId')->name('dosen.destroy');
    });

    /* ----------------------------- ADMIN ARAH ----------------------------- */
    Route::middleware(['role:admin_arah', 'audit'])->prefix('admin-arah')->name('admin-arah.')->group(function () {

        Route::get('/dashboard', [AdminArahController::class, 'index'])->name('dashboard');

        // Kampus
        Route::get('/kampus', [AdminArahKampusController::class, 'index'])->name('kampus.index');
        Route::get('/kampus/create', [AdminArahKampusController::class, 'create'])->name('kampus.create');
        Route::post('/kampus', [AdminArahKampusController::class, 'store'])->name('kampus.store');
        Route::get('/kampus/{kampusId}', [AdminArahKampusController::class, 'show'])->whereNumber('kampusId')->name('kampus.show');
        Route::get('/kampus/{kampusId}/edit', [AdminArahKampusController::class, 'edit'])->whereNumber('kampusId')->name('kampus.edit');
        Route::put('/kampus/{kampusId}', [AdminArahKampusController::class, 'update'])->whereNumber('kampusId')->name('kampus.update');
        Route::patch('/kampus/{kampusId}/status', [AdminArahKampusController::class, 'toggleStatus'])->whereNumber('kampusId')->name('kampus.toggle-status');

        // Admin kampus
        Route::get('/admin-kampus', [AdminArahAdminKampusController::class, 'index'])->name('admin-kampus.index');
        Route::get('/admin-kampus/create', [AdminArahAdminKampusController::class, 'create'])->name('admin-kampus.create');
        Route::post('/admin-kampus', [AdminArahAdminKampusController::class, 'store'])->name('admin-kampus.store');
        Route::get('/admin-kampus/{adminId}/edit', [AdminArahAdminKampusController::class, 'edit'])->whereNumber('adminId')->name('admin-kampus.edit');
        Route::put('/admin-kampus/{adminId}', [AdminArahAdminKampusController::class, 'update'])->whereNumber('adminId')->name('admin-kampus.update');
        Route::delete('/admin-kampus/{adminId}', [AdminArahAdminKampusController::class, 'destroy'])->whereNumber('adminId')->name('admin-kampus.destroy');
        Route::patch('/admin-kampus/{adminId}/status', [AdminArahAdminKampusController::class, 'toggleStatus'])->whereNumber('adminId')->name('admin-kampus.toggle-status');

        // Mahasiswa dan dosen global
        Route::get('/mahasiswa', [AdminArahMahasiswaController::class, 'index'])->name('mahasiswa.index');
        Route::get('/mahasiswa/{mahasiswaId}', [AdminArahMahasiswaController::class, 'show'])->whereNumber('mahasiswaId')->name('mahasiswa.show');
        Route::get('/dosen', [AdminArahDosenController::class, 'index'])->name('dosen.index');
        Route::get('/dosen/{dosenId}', [AdminArahDosenController::class, 'show'])->whereNumber('dosenId')->name('dosen.show');

        // Monitoring dan audit log
        Route::get('/monitoring', [AdminArahMonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/audit-log', [AdminArahAuditLogController::class, 'index'])->name('audit-log.index');
    });
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';