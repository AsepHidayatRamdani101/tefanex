<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignBriefController;
use App\Http\Controllers\MockupController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\UserController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::resource('users', UserController::class)->middleware('role:guru|super_admin');
Route::get('users-data', [UserController::class, 'data'])->name('users.data');

Route::resource('roles', RoleController::class)
    ->middleware('role:super_admin');
Route::get('roles-data', [RoleController::class, 'data'])->name('roles.data');

Route::resource('projects', ProjectController::class)
    ->middleware('role:guru|super_admin|kepala_tefa|siswa');
Route::get('projects-data', [ProjectController::class, 'data'])->name('projects.data');
Route::get('projects-get-id', [ProjectController::class, 'getProjectId'])->name('projects.getProjectId');

Route::resource('project-members', ProjectMemberController::class)
    ->middleware('role:guru|super_admin|kepala_tefa');
Route::get('projects/{project}/members', 
    [ProjectMemberController::class, 'data']
)->name('projects.members.data');
Route::resource('design-brief', DesignBriefController::class)
    ->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::get('design-brief-data', [DesignBriefController::class, 'data'])->name('design-brief.data');
Route::put('design-brief/{id}/status', [DesignBriefController::class, 'updateStatus'])->name('design-brief.status')->middleware('role:kepala_tefa');
Route::resource('timeline', TimelineController::class)
    ->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::get('timeline-data', [TimelineController::class, 'data'])->name('timeline.data');
Route::resource('mockup', MockupController::class)
    ->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::get('mockup-data', [MockupController::class, 'data'])->name('mockup.data');
Route::put('mockup/{id}/status', [MockupController::class, 'updateStatus'])->name('mockup.status')->middleware('role:kepala_tefa');
Route::resource('produksi', ProduksiController::class)
    ->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::get('produksi-data', [ProduksiController::class, 'data'])->name('produksi.data');
require __DIR__ . '/auth.php';
