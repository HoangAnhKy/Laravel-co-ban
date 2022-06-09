<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthLogin;
use Illuminate\Support\Facades\Route;


Route::get('Login', [UserController::class, 'index'])->name('Login.index');
Route::post('Login', [UserController::class, 'login'])->name('Login.login');
Route::get('Login/logout', [UserController::class, 'logout'])->name('Login.logout');
Route::get('Resign', [UserController::class, 'resign'])->name('resign');
Route::post('Resign/CreateAccount', [UserController::class, 'account'])->name('Resign.account');

Route::group([
    'middleware' => AuthLogin::class,
    // 'middleware' => 'admin', //c2, use kernel
], function (){

    Route::resource('course', CourseController::class)->except([
        'show',
    ]);

    Route::resource('student', StudentsController::class)->except([
        'show',
    ]);

    Route::get('course/API', [CourseController::class, 'api'])->name('course.api');
    Route::get('course/API/Name', [CourseController::class, 'apiName'])->name('course.api.name');

    Route::get('student/API', [StudentsController::class, 'api'])->name('student.api');
    Route::get('student/API/Name', [StudentsController::class, 'apiName'])->name('student.api.name');
});
