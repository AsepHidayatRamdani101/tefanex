<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignBriefController;
use App\Http\Controllers\MockupController;
use App\Http\Controllers\MassProController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\StudentTestController;
use App\Http\Controllers\StudentMaterialController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\QuestionController;
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
Route::get('project-members-data', [ProjectMemberController::class, 'data'])->name('project-members.data');
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
Route::put('produksi/{id}/status', [ProduksiController::class, 'updateStatus'])->name('produksi.status')->middleware('role:kepala_tefa');
Route::put('produksi/{id}/revisi', [ProduksiController::class, 'revisi'])->name('produksi.revisi')->middleware('role:kepala_tefa');

Route::resource('masspro', MassProController::class)
    ->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::get('masspro-data', [MassProController::class, 'data'])->name('masspro.data');
Route::put('masspro/{id}/status', [MassProController::class, 'updateStatus'])->name('masspro.status')->middleware('role:kepala_tefa');

Route::resource('invoices', InvoiceController::class)
    ->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::get('invoices-data', [InvoiceController::class, 'data'])->name('invoices.data');

Route::post('attendances/bulk', [AttendanceController::class, 'bulk'])->name('attendances.bulk')->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::resource('attendances', AttendanceController::class)
    ->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::get('attendances-data', [AttendanceController::class, 'data'])->name('attendances.data');

Route::post('materi/bulk', [MateriController::class, 'bulk'])->name('materi.bulk')->middleware('role:guru|super_admin|kepala_tefa');
Route::resource('materi', MateriController::class)
    ->middleware('role:guru|super_admin|kepala_tefa');
Route::get('materi-data', [MateriController::class, 'data'])->name('materi.data');

// Custom kelas routes BEFORE resource to avoid route conflicts
Route::middleware('role:guru|super_admin|kepala_tefa')->group(function () {
    Route::get('kelas/export', [KelasController::class, 'export'])->name('kelas.export');
    Route::get('kelas/download-template', [KelasController::class, 'downloadTemplate'])->name('kelas.downloadTemplate');
    Route::post('kelas/import', [KelasController::class, 'import'])->name('kelas.import');
    Route::get('kelas-data', [KelasController::class, 'data'])->name('kelas.data');
});
Route::resource('kelas', KelasController::class)
    ->middleware('role:guru|super_admin|kepala_tefa');

// Custom siswa routes BEFORE resource to avoid route conflicts
Route::middleware('role:guru|super_admin|kepala_tefa')->group(function () {
    Route::get('siswa/export', [SiswaController::class, 'export'])->name('siswa.export');
    Route::get('siswa/download-template', [SiswaController::class, 'downloadTemplate'])->name('siswa.downloadTemplate');
    Route::post('siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::get('siswa-data', [SiswaController::class, 'data'])->name('siswa.data');
});
Route::resource('siswa', SiswaController::class)
    ->middleware('role:guru|super_admin|kepala_tefa');

Route::resource('test', TestController::class)
    ->middleware('role:guru|super_admin|kepala_tefa');
Route::get('test-data', [TestController::class, 'data'])->name('test.data');
Route::get('nilai', [TestController::class, 'gradeIndex'])
    ->middleware('role:guru|super_admin|kepala_tefa')
    ->name('grades.index');
Route::get('nilai-data', [TestController::class, 'gradeData'])
    ->middleware('role:guru|super_admin|kepala_tefa')
    ->name('grades.data');

Route::resource('question', QuestionController::class)
    ->middleware('role:guru|super_admin|kepala_tefa');
Route::get('question-data', [QuestionController::class, 'data'])->name('question.data');
Route::post('question/import/excel', [QuestionController::class, 'importExcel'])->name('question.import.excel');
Route::post('question/import/word', [QuestionController::class, 'importWord'])->name('question.import.word');

Route::resource('quality-control', QualityController::class)
    ->middleware('role:siswa|guru|super_admin|kepala_tefa');
Route::get('quality-control-data', [QualityController::class, 'data'])->name('quality.data');
Route::put('quality-control/{id}/status', [QualityController::class, 'updateStatus'])->name('quality-control.status')->middleware('role:kepala_tefa');
Route::put('quality-control/{id}/revisi', [QualityController::class, 'revisi'])->name('quality-control.revisi')->middleware('role:kepala_tefa');

// Student Test Routes
Route::middleware('role:siswa')->group(function () {
    Route::get('student/tests', [StudentTestController::class, 'listTests'])->name('student.tests.list');
    Route::get('student/test/{test}', [StudentTestController::class, 'showTest'])->name('student.test.show');
    Route::post('student/test/{test}/submit', [StudentTestController::class, 'submitTest'])->name('student.test.submit');
    
    // Student Materials and Tasks Routes
    Route::get('student/materials', [StudentMaterialController::class, 'materials'])->name('student.materials.index');
    Route::get('student/tasks', [StudentMaterialController::class, 'tasks'])->name('student.tasks.index');
    Route::get('student/material/{material}', [StudentMaterialController::class, 'showMaterial'])->name('student.material.detail');
});

Route::group(['middleware' => 'role:siswa|guru|super_admin|kepala_tefa'], function () {
    Route::get('student/test/result/{result}', [StudentTestController::class, 'showResult'])->name('student.test.result');
});

Route::put('student/test/result/{result}', [StudentTestController::class, 'updateEvaluation'])
    ->middleware('role:guru|super_admin|kepala_tefa')
    ->name('student.test.result.update');


require __DIR__ . '/auth.php';
