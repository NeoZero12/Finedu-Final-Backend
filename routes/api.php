<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ModulPembelajaranController;
use App\Http\Controllers\ModulProgressController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\SimulasiController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiSimulasiController;
use App\Http\Controllers\DigitalNudgeController;
use App\Http\Controllers\HasilSkorController;
use App\Http\Controllers\KuesionerController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AdminRespondentController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminSimulationReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register/send-otp', [AuthController::class, 'sendRegisterOtp']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendPasswordResetOtp']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', fn (Request $request) => $request->user()->load('profil'));
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profil', [ProfilController::class, 'show']);
    Route::put('/profil', [ProfilController::class, 'update']);
    Route::get('/kuesioner', [KuesionerController::class, 'index']);
    Route::get('/modul-list/{modul}/kuesioner', [KuesionerController::class, 'showByModule']);
    Route::post('/modul-list/{modul}/kuesioner', [KuesionerController::class, 'storeByModule']);

    Route::get('/simulasi', [SimulasiController::class, 'index']);
    Route::post('/simulasi', [SimulasiController::class, 'store']);
    Route::get('/simulasi/laporan', [SimulasiController::class, 'report']);
    Route::get('/simulasi/{id}', [SimulasiController::class, 'show']);
    Route::put('/simulasi/{id}', [SimulasiController::class, 'update']);
    Route::post('/simulasi/transaksi', [TransaksiSimulasiController::class, 'store']);
    Route::put('/simulasi/transaksi/{transaksi}', [TransaksiSimulasiController::class, 'update']);
    Route::delete('/simulasi/transaksi/{transaksi}', [TransaksiSimulasiController::class, 'destroy']);
    Route::delete('/simulasi/{simulasi}/transaksi', [TransaksiSimulasiController::class, 'clear']);

    Route::get('/nudge', [DigitalNudgeController::class, 'index']);
    Route::post('/nudge/respons', [DigitalNudgeController::class, 'store']);
    Route::get('/forum-posts', [ForumController::class, 'index']);
    Route::post('/forum-posts', [ForumController::class, 'store']);
    Route::post('/forum-posts/{post}/comments', [ForumController::class, 'comment']);
    Route::get('/certificate', [CertificateController::class, 'show']);

    Route::get('/modul-list', [ModulPembelajaranController::class, 'index']);
    Route::get('/modul-list/{modul}', [ModulPembelajaranController::class, 'show']);
    Route::get('/modul-progress', [ModulProgressController::class, 'index']);
    Route::post('/modul-progress', [ModulProgressController::class, 'store']);
    Route::get('/produk-list', [ProdukController::class, 'index']);
    Route::get('/materi/{materi}', [MateriController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('modul', ModulPembelajaranController::class);
    Route::apiResource('produk', ProdukController::class);
    Route::get('/dashboard-analitik', [HasilSkorController::class, 'analisisEksperimen']);
    Route::get('/dashboard-analitik/export', [HasilSkorController::class, 'export']);
    Route::get('/kuesioner/export', [KuesionerController::class, 'export']);
    Route::get('/nudge/export', [DigitalNudgeController::class, 'export']);
    Route::get('/respondents', [AdminRespondentController::class, 'index']);
    Route::get('/respondents/export', [AdminRespondentController::class, 'export']);
    Route::put('/respondents/{user}', [AdminRespondentController::class, 'update']);
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::post('/admin/users', [AdminUserController::class, 'store']);
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update']);
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy']);
    Route::get('/admin/simulasi-reports', [AdminSimulationReportController::class, 'index']);
    Route::get('/admin/simulasi-reports/export', [AdminSimulationReportController::class, 'export']);
    Route::delete('/admin/simulasi-reports/{simulasi}', [AdminSimulationReportController::class, 'destroy']);
    Route::delete('/forum-posts/{post}', [ForumController::class, 'destroyPost']);
    Route::delete('/forum-comments/{comment}', [ForumController::class, 'destroyComment']);
});
