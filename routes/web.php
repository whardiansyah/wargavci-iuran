<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MasterPenghuniController;
use App\Http\Controllers\PencatatanAirController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\TransaksiKasController;
use App\Http\Controllers\MasterConfigController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\TabunganUmrohController;
use App\Http\Controllers\LaporanTabunganUmrohController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard/metode-bayar-detail', [DashboardController::class, 'metodeBayarDetail'])->middleware(['auth'])->name('dashboard.metode_bayar_detail');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User Management
    Route::resource('users', UserController::class);
    
    // RBAC Management
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    
    // Master Penghuni Management
    Route::resource('master_penghunis', MasterPenghuniController::class);
    Route::resource('anggota', AnggotaController::class)->parameters([
        'anggota' => 'anggota',
    ]);
    Route::post('pencatatan_air/import', [PencatatanAirController::class, 'import'])->name('pencatatan_air.import');
    Route::get('pencatatan_air/template', [PencatatanAirController::class, 'downloadTemplate'])->name('pencatatan_air.template');
    Route::get('pencatatan_air/export', [PencatatanAirController::class, 'exportExcel'])->name('pencatatan_air.export');
    Route::resource('pencatatan_air', PencatatanAirController::class);
    Route::post('tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate');
    Route::delete('tagihan/reset', [TagihanController::class, 'reset'])->name('tagihan.reset');
    Route::get('tagihan-rutin', [TagihanController::class, 'rutin'])->name('tagihan.rutin');
    Route::post('tagihan-rutin/pembayaran', [TagihanController::class, 'simpanPembayaran'])->name('tagihan.rutin.pembayaran');
    Route::resource('tagihan', TagihanController::class);
    Route::get('transaksi_kas/export', [TransaksiKasController::class, 'exportExcel'])->name('transaksi_kas.export');
    Route::resource('transaksi_kas', TransaksiKasController::class)->parameters([
        'transaksi_kas' => 'transaksiKas',
    ]);

    // Master Config Management
    Route::resource('master_configs', MasterConfigController::class);
    
    // Penyewa Management
    Route::resource('penyewa', PenyewaController::class);

    // Tabungan Umroh
    Route::resource('tabungan_umroh', TabunganUmrohController::class)->parameters([
        'tabungan_umroh' => 'tabunganUmroh',
    ]);

    // Laporan
    Route::get('laporan/tabungan-umroh', [LaporanTabunganUmrohController::class, 'index'])->name('laporan.tabungan_umroh');
    Route::get('laporan/tabungan-umroh/detail/{anggotaId}', [LaporanTabunganUmrohController::class, 'detail'])->name('laporan.tabungan_umroh.detail');
});

require __DIR__.'/auth.php';
