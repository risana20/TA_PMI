<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Public\BerandaController;
use App\Http\Controllers\Public\KebutuhanMendesakController;
use App\Http\Controllers\Public\ArtikelPublikController;
use App\Http\Controllers\User\DonasiUserController;
use App\Http\Controllers\User\KunjunganUserController;
use App\Http\Controllers\User\CekStatusController;
use App\Http\Controllers\User\ProfilController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WargaBinaanController;
use App\Http\Controllers\Admin\LogistikController;
use App\Http\Controllers\Admin\KunjunganController;
use App\Http\Controllers\Admin\DonasiController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\KeuanganController;
use App\Http\Controllers\Admin\ReimbursementController;
use App\Http\Controllers\Admin\ArtikelController;
use App\Http\Controllers\Admin\ManajemenAkunPublikController;
use App\Http\Controllers\Admin\ItemLogistikController;
use App\Http\Controllers\Superadmin\AccReimbursementController;
use App\Http\Controllers\Superadmin\ManajemenAkunGriyaController;

/*
|--------------------------------------------------------------------------
| Public Routes (tanpa login)
|--------------------------------------------------------------------------
*/

Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/kebutuhan-mendesak', [KebutuhanMendesakController::class, 'index'])->name('kebutuhan-mendesak');
Route::get('/artikel', [ArtikelPublikController::class, 'index'])->name('artikel.index');
Route::get('/artikel/{slug}', [ArtikelPublikController::class, 'show'])->name('artikel.show');
Route::get('/kunjungan', [KunjunganUserController::class, 'index'])->name('kunjungan.index');
Route::post('/kunjungan', [KunjunganUserController::class, 'store'])->name('kunjungan.store');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');

    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPasswordPost'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPasswordPost'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Email Verification Routes
|--------------------------------------------------------------------------
*/
Route::get('/email/verify', function () {
    return view('pages.auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
    $request->fulfill();

    $user = $request->user();
    if ($user->hasRole('superadmin')) {
        return redirect()->route('superadmin.dashboard');
    } elseif ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('beranda')->with('success', 'Email Anda berhasil diverifikasi!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| User / Donatur Routes (auth only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/donasi', [DonasiUserController::class, 'index'])->name('donasi.index');
    Route::post('/donasi', [DonasiUserController::class, 'store'])->name('donasi.store');

   

    Route::get('/kunjungan/search-wbp', [KunjunganUserController::class, 'searchWbp'])->name('kunjungan.search-wbp');
    Route::get('/cek-status', [CekStatusController::class, 'index'])->name('cek-status.index');

    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password', [ProfilController::class, 'changePassword'])->name('profil.change-password');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (role: admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Warga Binaan
    Route::resource('warga-binaan', WargaBinaanController::class);
    // Export: PDF & Excel
    Route::get('/warga-binaan/export/pdf', [WargaBinaanController::class, 'exportPdf'])->name('warga-binaan.export.pdf');
    Route::get('/warga-binaan/export/excel', [WargaBinaanController::class, 'exportExcel'])->name('warga-binaan.export.excel');

    // Logistik
    Route::resource('logistik', LogistikController::class);
    Route::post('/logistik/{logistik}/stok', [LogistikController::class, 'updateStok'])->name('logistik.update-stok');
    Route::post('/logistik/{logistik}/toggle-publik', [LogistikController::class, 'togglePublik'])->name('logistik.toggle-publik');
    Route::post('/logistik/{logistik}/pemasukan', [LogistikController::class, 'storePemasukan'])->name('logistik.store-pemasukan');
    Route::post('/logistik/{logistik}/pengeluaran', [LogistikController::class, 'storePengeluaran'])->name('logistik.store-pengeluaran');
    Route::post('/item-logistik/store',[ItemLogistikController::class,'store'])->name('item-logistik.store');

    // Kunjungan
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::post('/kunjungan/store', [KunjunganController::class, 'store'])->name('kunjungan.store');
    Route::post('/kunjungan/{kunjungan}/approve', [KunjunganController::class, 'approve'])->name('kunjungan.approve');
    Route::post('/kunjungan/{kunjungan}/reject', [KunjunganController::class, 'reject'])->name('kunjungan.reject');
    Route::get('/kunjungan/export/pdf', [KunjunganController::class, 'exportPdf'])->name('kunjungan.export.pdf');
    Route::get('/kunjungan/export/excel', [KunjunganController::class, 'exportExcel'])->name('kunjungan.export.excel');
    // Donasi
    Route::get('/donasi', [DonasiController::class, 'index'])->name('donasi.index');
    Route::post('/donasi', [DonasiController::class, 'store'])->name('donasi.store');
    Route::get('/donasi/{donasi}', [DonasiController::class, 'show'])->name('donasi.show');
    Route::post('/donasi/{donasi}/verify', [DonasiController::class, 'verify'])->name('donasi.verify');
    Route::post('/donasi/{donasi}/complete', [DonasiController::class, 'complete'])->name('donasi.complete');
    Route::post('/donasi/{donasi}/reject', [DonasiController::class, 'reject'])->name('donasi.reject');

    // Monitoring Kesehatan
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{wargaBinaan}', [MonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/monitoring/{wargaBinaan}/pemeriksaan/export/pdf', [MonitoringController::class, 'exportPemeriksaanPdf'])->name('monitoring.exportPemeriksaanPdf');
    Route::get('/monitoring/{wargaBinaan}/pemeriksaan/export/excel', [MonitoringController::class, 'exportPemeriksaanExcel'])->name('monitoring.exportPemeriksaanExcel');
    Route::post('/monitoring/{wargaBinaan}/pemeriksaan', [MonitoringController::class, 'storePemeriksaan'])->name('monitoring.storePemeriksaan');
    Route::post('/monitoring/{wargaBinaan}/rsj', [MonitoringController::class, 'storeRSJ'])->name('monitoring.storeRSJ');
    Route::post('/monitoring/{wargaBinaan}/rujukan', [MonitoringController::class, 'storeRujukan'])->name('monitoring.storeRujukan');
    Route::post('/monitoring/{wargaBinaan}/riwayat-penyakit', [MonitoringController::class, 'storeRiwayatPenyakit'])->name('monitoring.storeRiwayatPenyakit');
    Route::put('/monitoring/{wargaBinaan}/riwayat-penyakit/{riwayatPenyakit}/status', [MonitoringController::class, 'updateStatusRiwayatPenyakit'])->name('monitoring.updateStatusRiwayatPenyakit');
    Route::post('/monitoring/{wargaBinaan}/obat', [MonitoringController::class, 'storeObat'])->name('monitoring.storeObat');
    Route::get('/monitoring/{wargaBinaan}/obat/{stokObat}', [MonitoringController::class, 'showObat'])->name('monitoring.showObat');
    Route::put('/monitoring/{wargaBinaan}/obat/{stokObat}/status', [MonitoringController::class, 'updateStatusObat'])->name('monitoring.updateStatusObat');
    Route::post('/monitoring/{wargaBinaan}/obat/{stokObat}/pengeluaran', [MonitoringController::class, 'storePengeluaranObat'])->name('monitoring.storePengeluaranObat');
    Route::put('/monitoring/{wargaBinaan}/obat/{stokObat}/sisa', [MonitoringController::class, 'updateSisaObat'])->name('monitoring.updateSisaObat');
    Route::put('/monitoring/obat/{stokObat}', [MonitoringController::class, 'updateObat'])->name('monitoring.updateObat');

    // Keuangan
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::post('/keuangan/pemasukan', [KeuanganController::class, 'storePemasukan'])->name('keuangan.storePemasukan');
    Route::post('/keuangan/pengeluaran', [KeuanganController::class, 'storePengeluaran'])->name('keuangan.storePengeluaran');

    // Reimbursement (Admin hanya bisa ajukan)
    Route::get('/reimbursement', [ReimbursementController::class, 'index'])->name('reimbursement.index');
    Route::post('/reimbursement', [ReimbursementController::class, 'store'])->name('reimbursement.store');
    Route::get('/reimbursement/export/pdf', [ReimbursementController::class, 'exportPdf'])->name('reimbursement.export.pdf');
    Route::get('/reimbursement/export/excel', [ReimbursementController::class, 'exportExcel'])->name('reimbursement.export.excel');

    // Artikel
    Route::resource('artikel', ArtikelController::class);
    Route::get('/artikel/export/pdf', [ArtikelController::class, 'exportPdf'])->name('artikel.export.pdf');
    Route::get('/artikel/export/excel', [ArtikelController::class, 'exportExcel'])->name('artikel.export.excel');

    // Manajemen Akun Publik
    Route::get('/akun-publik', [ManajemenAkunPublikController::class, 'index'])->name('akun-publik.index');
    Route::post('/akun-publik/{user}/toggle', [ManajemenAkunPublikController::class, 'toggleStatus'])->name('akun-publik.toggle');
});

/*
|--------------------------------------------------------------------------
| Superadmin Routes (role: superadmin only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {

    // Shared Routes (duplicated from admin)
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Warga Binaan
    Route::resource('warga-binaan', WargaBinaanController::class);
    Route::get('/warga-binaan/export/pdf', [WargaBinaanController::class, 'exportPdf'])->name('warga-binaan.export.pdf');
    Route::get('/warga-binaan/export/excel', [WargaBinaanController::class, 'exportExcel'])->name('warga-binaan.export.excel');

    // Logistik
    Route::resource('logistik', LogistikController::class);
    Route::post('/logistik/{logistik}/stok', [LogistikController::class, 'updateStok'])->name('logistik.update-stok');
    Route::post('/logistik/{logistik}/toggle-publik', [LogistikController::class, 'togglePublik'])->name('logistik.toggle-publik');
    Route::post('/logistik/{logistik}/pemasukan', [LogistikController::class, 'storePemasukan'])->name('logistik.store-pemasukan');
    Route::post('/logistik/{logistik}/pengeluaran', [LogistikController::class, 'storePengeluaran'])->name('logistik.store-pengeluaran');

    // Kunjungan
    Route::get('/kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::post('/kunjungan/store', [KunjunganController::class, 'store'])->name('kunjungan.store');
    Route::post('/kunjungan/{kunjungan}/approve', [KunjunganController::class, 'approve'])->name('kunjungan.approve');
    Route::post('/kunjungan/{kunjungan}/reject', [KunjunganController::class, 'reject'])->name('kunjungan.reject');
    Route::get('/kunjungan/export/pdf', [KunjunganController::class, 'exportPdf'])->name('kunjungan.export.pdf');
    Route::get('/kunjungan/export/excel', [KunjunganController::class, 'exportExcel'])->name('kunjungan.export.excel');
    // Donasi
    Route::get('/donasi', [DonasiController::class, 'index'])->name('donasi.index');
    Route::post('/donasi', [DonasiController::class, 'store'])->name('donasi.store');
    Route::get('/donasi/{donasi}', [DonasiController::class, 'show'])->name('donasi.show');
    Route::post('/donasi/{donasi}/verify', [DonasiController::class, 'verify'])->name('donasi.verify');
    Route::post('/donasi/{donasi}/complete', [DonasiController::class, 'complete'])->name('donasi.complete');
    Route::post('/donasi/{donasi}/reject', [DonasiController::class, 'reject'])->name('donasi.reject');

    // Monitoring Kesehatan
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{wargaBinaan}', [MonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/monitoring/{wargaBinaan}/pemeriksaan/export/pdf', [MonitoringController::class, 'exportPemeriksaanPdf'])->name('monitoring.exportPemeriksaanPdf');
    Route::get('/monitoring/{wargaBinaan}/pemeriksaan/export/excel', [MonitoringController::class, 'exportPemeriksaanExcel'])->name('monitoring.exportPemeriksaanExcel');
    Route::post('/monitoring/{wargaBinaan}/pemeriksaan', [MonitoringController::class, 'storePemeriksaan'])->name('monitoring.storePemeriksaan');
    Route::post('/monitoring/{wargaBinaan}/rsj', [MonitoringController::class, 'storeRSJ'])->name('monitoring.storeRSJ');
    Route::post('/monitoring/{wargaBinaan}/rujukan', [MonitoringController::class, 'storeRujukan'])->name('monitoring.storeRujukan');
    Route::post('/monitoring/{wargaBinaan}/riwayat-penyakit', [MonitoringController::class, 'storeRiwayatPenyakit'])->name('monitoring.storeRiwayatPenyakit');
    Route::put('/monitoring/{wargaBinaan}/riwayat-penyakit/{riwayatPenyakit}/status', [MonitoringController::class, 'updateStatusRiwayatPenyakit'])->name('monitoring.updateStatusRiwayatPenyakit');
    Route::post('/monitoring/{wargaBinaan}/obat', [MonitoringController::class, 'storeObat'])->name('monitoring.storeObat');
    Route::get('/monitoring/{wargaBinaan}/obat/{stokObat}', [MonitoringController::class, 'showObat'])->name('monitoring.showObat');
    Route::put('/monitoring/{wargaBinaan}/obat/{stokObat}/status', [MonitoringController::class, 'updateStatusObat'])->name('monitoring.updateStatusObat');
    Route::post('/monitoring/{wargaBinaan}/obat/{stokObat}/pengeluaran', [MonitoringController::class, 'storePengeluaranObat'])->name('monitoring.storePengeluaranObat');
    Route::put('/monitoring/{wargaBinaan}/obat/{stokObat}/sisa', [MonitoringController::class, 'updateSisaObat'])->name('monitoring.updateSisaObat');
    Route::put('/monitoring/obat/{stokObat}', [MonitoringController::class, 'updateObat'])->name('monitoring.updateObat');

    // Keuangan
    Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::post('/keuangan/pemasukan', [KeuanganController::class, 'storePemasukan'])->name('keuangan.storePemasukan');
    Route::post('/keuangan/pengeluaran', [KeuanganController::class, 'storePengeluaran'])->name('keuangan.storePengeluaran');

    // Reimbursement (Superadmin juga bisa melihat atau ajukan jika dibutuhkan, though usually ACC)
    Route::get('/reimbursement', [ReimbursementController::class, 'index'])->name('reimbursement.index');
    Route::post('/reimbursement', [ReimbursementController::class, 'store'])->name('reimbursement.store');

    // Artikel
    Route::resource('artikel', ArtikelController::class);
    Route::get('/artikel/export/pdf', [ArtikelController::class, 'exportPdf'])->name('artikel.export.pdf');
    Route::get('/artikel/export/excel', [ArtikelController::class, 'exportExcel'])->name('artikel.export.excel');

    // Manajemen Akun Publik
    Route::get('/akun-publik', [ManajemenAkunPublikController::class, 'index'])->name('akun-publik.index');
    Route::post('/akun-publik/{user}/toggle', [ManajemenAkunPublikController::class, 'toggleStatus'])->name('akun-publik.toggle');

    // ACC Reimbursement
    Route::get('/acc-reimbursement', [AccReimbursementController::class, 'index'])->name('acc-reimbursement.index');
    Route::post('/acc-reimbursement/{reimbursement}/validasi', [AccReimbursementController::class, 'validasi'])->name('acc-reimbursement.validasi');
    Route::post('/acc-reimbursement/{reimbursement}/batalkan', [AccReimbursementController::class, 'batalkan'])->name('acc-reimbursement.batalkan');
    Route::get('/acc-reimbursement/export/pdf', [AccReimbursementController::class, 'exportPdf'])->name('acc-reimbursement.export.pdf');
    Route::get('/acc-reimbursement/export/excel', [AccReimbursementController::class, 'exportExcel'])->name('acc-reimbursement.export.excel');

    // Manajemen Akun Griya
    Route::get('/akun-griya', [ManajemenAkunGriyaController::class, 'index'])->name('akun-griya.index');
    Route::post('/akun-griya', [ManajemenAkunGriyaController::class, 'store'])->name('akun-griya.store');
    Route::put('/akun-griya/{user}', [ManajemenAkunGriyaController::class, 'update'])->name('akun-griya.update');
    Route::post('/akun-griya/{user}/toggle', [ManajemenAkunGriyaController::class, 'toggleStatus'])->name('akun-griya.toggle');
});
