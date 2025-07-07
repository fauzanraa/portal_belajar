<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManageAnswerController;
use App\Http\Controllers\ManageClassController;
use App\Http\Controllers\ManageMaterialController;
use App\Http\Controllers\ManageMaterialSessionController;
use App\Http\Controllers\ManageMeetingController;
use App\Http\Controllers\ManageSchoolController;
use App\Http\Controllers\ManageProgressController;
use App\Http\Controllers\ManageScoreController;
use App\Http\Controllers\ManageStudentController;
use App\Http\Controllers\ManageTaskController;
use App\Http\Controllers\ManageTaskSessionController;
use App\Http\Controllers\ManageTeacherController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\StudentAnswerController;
use App\Http\Controllers\StudentMeetingController;
use App\Http\Controllers\StudentScoreController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomepageController::class, 'welcome'])->name('welcome');

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/postLogin', [LoginController::class, 'postLogin'])->name('postLogin');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// admin
Route::prefix('admin')->middleware('role:admin')->group(function () {
    Route::get('/', [HomepageController::class, 'indexAdmin'])->name('admin-index');
    // Route::get('/manage-schools', [ManageSchoolController::class, 'index'])->name('manage-schools');
    // Route::post('/manage-schools/store', [ManageSchoolController::class, 'store'])->name('store-schools');
    // Route::put('/manage-school/update', [ManageSchoolController::class, 'update'])->name('update-schools');
    // Route::delete('/manage-school/delete/{id}', [ManageSchoolController::class, 'delete'])->name('delete-schools');
    Route::get('/manage-class', [ManageClassController::class, 'index'])->name('manage-class');
    Route::post('/manage-class/store', [ManageClassController::class, 'store'])->name('store-class');
    Route::post('/manage-schools/update', [ManageClassController::class, 'update'])->name('update-class');
    Route::delete('/manage-school/delete/{id}', [ManageSchoolController::class, 'delete'])->name('delete-class');
    Route::get('/manage-teachers', [ManageTeacherController::class, 'index'])->name('manage-teachers');
    Route::post('/manage-teachers/store', [ManageTeacherController::class, 'store'])->name('store-teachers');
    Route::post('/manage-teachers/update', [ManageTeacherController::class, 'update'])->name('update-teachers');
    Route::delete('/manage-teachers/delete/{id}', [ManageTeacherController::class, 'delete'])->name('delete-teachers');
    Route::get('/manage-students', [ManageStudentController::class, 'index'])->name('manage-students');
    // Route::get('manage-students/get-classes', [ManageStudentController::class, 'getClasses']);
    Route::post('/manage-students/store', [ManageStudentController::class, 'store'])->name('store-students');
    Route::put('/manage-students/update', [ManageStudentController::class, 'update'])->name('update-students');
    Route::delete('/manage-students/delete/{id}', [ManageStudentController::class, 'delete'])->name('delete-students');
    Route::get('/manage-users', [ManageUserController::class, 'index'])->name('manage-users');
    Route::post('/manage-users/sync', [ManageUserController::class, 'sync'])->name('sync-users');
    Route::put('/manage-users/update', [ManageUserController::class, 'update'])->name('update-users');
    Route::delete('/manage-users/delete/{id}', [ManageUserController::class, 'delete'])->name('delete-users');
});

// guru
Route::prefix('teacher')->middleware('role:guru')->group(function () {
    Route::get('/', [HomepageController::class, 'indexGuru'])->name('teacher-index');
    Route::get('/manage-meetings', [ManageMeetingController::class, 'index'])->name('manage-meetings');
    Route::post('/manage-meetings/store', [ManageMeetingController::class, 'store'])->name('store-meetings');
    Route::put('/manage-meetings/update', [ManageMeetingController::class, 'update'])->name('update-meetings');
    Route::delete('/manage-meetings/delete/{id}', [ManageMeetingController::class, 'delete'])->name('delete-meetings');
    Route::get('/manage-meetings/{id}', [ManageMeetingController::class, 'indexMaterial'])->name('manage-materials');
    Route::post('/manage-meetings/{id}/store-material', [ManageMaterialController::class, 'store'])->name('store-materials');
    Route::post('/manage-meetings/{id}/store-task', [ManageTaskController::class, 'store'])->name('store-tasks');
    Route::get('/manage-materials/{id}', [ManageMaterialController::class, 'index'])->name('detail-materials');
    Route::post('/manage-materials/{id}/file', [ManageMaterialController::class, 'storeFile'])->name('file-materials');
    Route::get('/session-materials/{id}', [ManageMaterialSessionController::class, 'index'])->name('session-materials');
    Route::post('/session-materials/{id}/store', [ManageMaterialSessionController::class, 'store'])->name('store-sessions');
    Route::get('/manage-tasks/{id}', [ManageTaskController::class, 'index'])->name('detail-tasks');
    Route::put('/manage-tasks/{id}/update', [ManageTaskController::class, 'update'])->name('update-tasks');
    Route::get('/manage-tasks/{id}/question', [ManageTaskController::class, 'question'])->name('question-tasks');
    Route::post('/manage-tasks/{id}/store-question', [ManageTaskController::class, 'storeQuestion'])->name('store-questions');
    Route::post('/manage-tasks/{id}/update-components', [ManageTaskController::class, 'updateComponentSettings'])->name('update-components');
    Route::get('/manage-tasks/{id}/draw-answer', [ManageAnswerController::class, 'index'])->name('draw-correct-answer');
    Route::post('/manage-tasks/{id}/draw-answer/store', [ManageAnswerController::class, 'store'])->name('store-correct-answer');
    Route::get('/manage-tasks/{id}/draw-answer/edit', [ManageAnswerController::class, 'editAnswer'])->name('edit-correct-answer');
    Route::get('/manage-tasks/{id}/{type}', [ManageTaskSessionController::class, 'index'])->name('session-tasks');
    Route::post('/manage-tasks/{id}/{type}/store', [ManageTaskSessionController::class, 'store'])->name('store-sessions');
    Route::get('/manage-scores', [ManageScoreController::class, 'index'])->name('manage-scores');
    Route::get('/manage-scores/{idModul}', [ManageScoreController::class, 'detail'])->name('detail-moduls');
    Route::get('/manage-scores/{idModul}/{idSession}', [ManageScoreController::class, 'assessment'])->name('detail-assessments');
    Route::post('/manage-scores/{idModul}/{idSession}/store', [ManageScoreController::class, 'store'])->name('store-assessments');
    Route::get('/manage-progress', [ManageProgressController::class, 'index'])->name('manage-progress');
    Route::get('/manage-progress/{idStudent}', [ManageProgressController::class, 'detail'])->name('detail-progress');
    Route::get('/manage-progress/{idStudent}/summary', [ManageProgressController::class, 'summary'])->name('summary-progress');
});

// siswa
Route::prefix('student')->middleware('role:siswa')->group(function () {
    Route::get('/', [HomepageController::class, 'indexSiswa'])->name('student-index');
    Route::get('/meetings', [StudentMeetingController::class, 'index'])->name('list-teachers');
    Route::get('/meetings/{idTeacher}', [StudentMeetingController::class, 'listMeeting'])->name('list-meetings');
    Route::get('/meetings/{idTeacher}/{idMeeting}', [StudentMeetingController::class, 'detailMeeting'])->name('detail-meetings');
    Route::get('/draw/{idTask}', [StudentAnswerController::class, 'index'])->name('draw-flowchart');
    Route::post('/draw/{idTask}/store', [StudentAnswerController::class, 'store'])->name('store-flowchart');
    Route::get('/summary/{idTask}', [StudentAnswerController::class, 'summary'])->name('summary');
    Route::get('/scores', [StudentScoreController::class, 'index'])->name('list-scores');
});
