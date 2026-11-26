<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeviceRequestController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\KnowledgeBaseController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\OperationApprovalController;
use App\Http\Controllers\Admin\RegistrationReviewController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterRtRwController;
use App\Http\Controllers\Public\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website Routes (Section 16 - No Auth Required)
|--------------------------------------------------------------------------
*/
Route::get('/', [PublicController::class, 'home'])->name('public.home');
Route::get('/tentang', [PublicController::class, 'about'])->name('public.about');
Route::get('/cara-kerja', [PublicController::class, 'howItWorks'])->name('public.how-it-works');
Route::get('/teknologi', [PublicController::class, 'technology'])->name('public.technology');
Route::get('/gis', [PublicController::class, 'gis'])->name('public.gis');
Route::get('/peta-sebaran', [PublicController::class, 'gis'])->name('public.coverage');
Route::get('/statistik', [PublicController::class, 'stats'])->name('public.stats');
Route::get('/dampak-lingkungan', [PublicController::class, 'stats'])->name('public.impact');
Route::get('/berita', [PublicController::class, 'news'])->name('public.news');
Route::get('/faq', [PublicController::class, 'faq'])->name('public.faq');
Route::get('/kontak', [PublicController::class, 'contact'])->name('public.contact');
Route::get('/chatbot', [PublicController::class, 'chatbotPage'])->name('public.chatbot.page');
Route::post('/api/public/chatbot', [PublicController::class, 'chatbot'])->name('public.chatbot');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guests Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/daftar-rtrw', [RegisterRtRwController::class, 'showRegistrationForm'])->name('register.rtrw');
    Route::post('/daftar-rtrw', [RegisterRtRwController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Administrator Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // 1. Dashboard (Master Admin, Admin Kota, Admin RT/RW)
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:master_admin,admin_kota,admin_rtrw')
        ->name('admin.dashboard');

    // 2. Monitoring & Remote Control (All 4 roles)
    Route::prefix('/admin/monitoring')->name('admin.monitoring.')->group(function () {
        Route::get('/', [MonitoringController::class, 'index'])->name('index');
        Route::post('/{device}/request-permission', [MonitoringController::class, 'requestPermission'])->name('request-permission');
        Route::post('/{device}/send-command', [MonitoringController::class, 'sendCommand'])->name('send-command');
    });

    // 3. Persetujuan Pengoperasian (Master Admin, Admin Kota, Admin RT/RW)
    Route::prefix('/admin/persetujuan')->name('admin.approvals.')->middleware('role:master_admin,admin_kota,admin_rtrw')->group(function () {
        Route::get('/', [OperationApprovalController::class, 'index'])->name('index');
        Route::post('/{permission}/respond', [OperationApprovalController::class, 'respond'])->name('respond');
    });

    // 4. Kondisi Alat & Laporan Kerusakan (All 4 roles)
    Route::prefix('/admin/kondisi-alat')->name('admin.maintenance.')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index'])->name('index');
        Route::post('/', [MaintenanceController::class, 'store'])->name('store');
        Route::post('/{report}/status', [MaintenanceController::class, 'updateStatus'])->name('update-status');
    });

    // 5. Laporan & Rekapitulasi Reduksi Massa (Master Admin, Admin Kota, Admin RT/RW)
    Route::prefix('/admin/laporan')->name('admin.reports.')->middleware('role:master_admin,admin_kota,admin_rtrw')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-csv', [ReportController::class, 'exportCsv'])->name('export-csv');
    });

    // 6. Verifikasi & Pendaftaran RT/RW (Master Admin, Admin Kota)
    Route::prefix('/admin/pendaftaran-rtrw')->name('admin.registration-requests.')->middleware('role:master_admin,admin_kota')->group(function () {
        Route::get('/', [RegistrationReviewController::class, 'index'])->name('index');
        Route::post('/{registrationRequest}/approve', [RegistrationReviewController::class, 'approve'])->name('approve');
        Route::post('/{registrationRequest}/reject', [RegistrationReviewController::class, 'reject'])->name('reject');
    });

    // 7. Inventaris & Distribusi Perangkat (Master Admin, Admin Kota)
    Route::prefix('/admin/inventaris')->name('admin.inventory.')->middleware('role:master_admin,admin_kota')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::post('/central-stock', [InventoryController::class, 'addCentralStock'])->name('add-central-stock');
        Route::put('/devices/{device}', [InventoryController::class, 'updateDevice'])->name('update-device');
        Route::delete('/devices/{device}', [InventoryController::class, 'destroyDevice'])->name('destroy-device');
        Route::post('/allocate-city', [InventoryController::class, 'allocateToCity'])->name('allocate-to-city');
        Route::post('/distribute-rtrw', [InventoryController::class, 'distributeToRtRw'])->name('distribute-to-rtrw');
    });

    // 8. Pengajuan Penambahan Unit Alat (Master Admin, Admin Kota, Admin RT/RW)
    Route::prefix('/admin/permintaan-alat')->name('admin.device-requests.')->middleware('role:master_admin,admin_kota,admin_rtrw')->group(function () {
        Route::get('/', [DeviceRequestController::class, 'index'])->name('index');
        Route::post('/', [DeviceRequestController::class, 'store'])->name('store');
        Route::post('/{deviceRequest}/status', [DeviceRequestController::class, 'updateStatus'])->name('update-status');
    });

    // 9. Jadwal Petugas & Manajemen Petugas Lapangan (Master Admin, Admin RT/RW)
    Route::prefix('/admin/jadwal')->name('admin.schedules.')->middleware('role:master_admin,admin_rtrw')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/', [ScheduleController::class, 'store'])->name('store');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/petugas', [ScheduleController::class, 'createOperator'])->name('create-operator');
    });

    // 10. Kelola Knowledge Base RAG AI (Master Admin, Admin Kota)
    Route::prefix('/admin/knowledge-base')->name('admin.knowledge-base.')->middleware('role:master_admin,admin_kota')->group(function () {
        Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
        Route::post('/', [KnowledgeBaseController::class, 'store'])->name('store');
        Route::delete('/{knowledgeDocument}', [KnowledgeBaseController::class, 'destroy'])->name('destroy');
    });

    // 11. Audit Log Sistem (Master Admin, Admin Kota)
    Route::prefix('/admin/audit-logs')->name('admin.audit-logs.')->middleware('role:master_admin,admin_kota')->group(function () {
        Route::get('/', [AuditLogController::class, 'index'])->name('index');
        Route::post('/verify', [AuditLogController::class, 'verify'])->name('verify');
    });
});
