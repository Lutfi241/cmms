<?php

use App\Http\Controllers\SiteController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\LocationAreaController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetCategoryController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\SparePartController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/**
 * Helper untuk mendaftarkan route CRUD suatu modul, dengan permission
 * yang berbeda untuk setiap aksi (view/create/edit/delete) sesuai
 * hasil desain RBAC. Dipakai berulang untuk setiap modul CMMS,
 * supaya tidak perlu menulis blok route yang sama berkali-kali.
 */
function registerModuleRoutes(string $uri, string $controller, string $module): void
{
    $param = Str::singular($uri);

    Route::middleware(['auth', "permission:view_{$module}"])
        ->get("/{$uri}", [$controller, 'index'])->name("{$uri}.index");

    Route::middleware(['auth', "permission:create_{$module}"])->group(function () use ($uri, $controller) {
        Route::get("/{$uri}/create", [$controller, 'create'])->name("{$uri}.create");
        Route::post("/{$uri}", [$controller, 'store'])->name("{$uri}.store");
    });

    Route::middleware(['auth', "permission:edit_{$module}"])->group(function () use ($uri, $controller, $param) {
        Route::get("/{$uri}/{{$param}}/edit", [$controller, 'edit'])->name("{$uri}.edit");
        Route::put("/{$uri}/{{$param}}", [$controller, 'update'])->name("{$uri}.update");
    });

    Route::middleware(['auth', "permission:delete_{$module}"])
        ->delete("/{$uri}/{{$param}}", [$controller, 'destroy'])->name("{$uri}.destroy");
}

registerModuleRoutes('asset_categories', AssetCategoryController::class, 'asset_categories');
registerModuleRoutes('sites', SiteController::class, 'sites');
registerModuleRoutes('buildings', BuildingController::class, 'buildings');
registerModuleRoutes('floors', FloorController::class, 'floors');
registerModuleRoutes('location_areas', LocationAreaController::class, 'location_areas');
registerModuleRoutes('assets', AssetController::class, 'assets');
registerModuleRoutes('work_orders', WorkOrderController::class, 'work_orders');
registerModuleRoutes('spare_parts', SparePartController::class, 'spare_parts');
registerModuleRoutes('users', UserController::class, 'users');

/**
 * Route khusus state machine Work Order.
 * Masing-masing punya permission sendiri sesuai desain RBAC:
 * - approve/reject : Manager & Super Admin
 * - assign         : Admin Site, Manager, Supervisor Maintenance & Super Admin
 * - start/complete : Teknisi & Super Admin
 */
Route::middleware(['auth', 'permission:view_work_orders'])
    ->get('/work_orders/{work_order}', [WorkOrderController::class, 'show'])->name('work_orders.show');

Route::middleware(['auth', 'permission:execute_work_orders'])->group(function () {
    Route::post('/work_orders/{work_order}/parts', [WorkOrderController::class, 'addPart'])->name('work_orders.parts.add');
    Route::delete('/work_orders/{work_order}/parts/{work_order_part}', [WorkOrderController::class, 'removePart'])->name('work_orders.parts.remove');
});

Route::middleware(['auth', 'permission:approve_work_orders'])->group(function () {
    Route::post('/work_orders/{work_order}/approve', [WorkOrderController::class, 'approve'])->name('work_orders.approve');
    Route::post('/work_orders/{work_order}/reject', [WorkOrderController::class, 'reject'])->name('work_orders.reject');
});

Route::middleware(['auth', 'permission:assign_work_orders'])
    ->post('/work_orders/{work_order}/assign', [WorkOrderController::class, 'assignTechnician'])->name('work_orders.assign');

Route::middleware(['auth', 'permission:execute_work_orders'])->group(function () {
    Route::post('/work_orders/{work_order}/start', [WorkOrderController::class, 'start'])->name('work_orders.start');
    Route::post('/work_orders/{work_order}/complete', [WorkOrderController::class, 'complete'])->name('work_orders.complete');
});

/**
 * Route Roles (read-only). Melihat daftar role dan detail permission
 * di dalamnya. Hanya butuh permission view_roles.
 */
Route::middleware(['auth', 'permission:view_roles'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
});

require __DIR__.'/auth.php';
