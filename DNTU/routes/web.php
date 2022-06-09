<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('Login', [UserController::class, 'login'])->name('login');
Route::post('Login/checkin', [UserController::class, 'checkout'])->name('checkout');
Route::get('Resigner', [UserController::class, 'resigner'])->name('resigner');
Route::post('Login/checkin', [UserController::class, 'checkout'])->name('checkout');

Route::get('/auth/redirect/{provider}', function ($provider) {
    return Socialite::driver($provider)->redirect();
})->name('auth.redirect');

Route::get('/auth/callback/{provider}', [UserController::class, 'callback']);

Route::group([
    'middleware' => 'admin',
],
    function () {
        Route::resource('Courses', CourseController::class)->except(['show']);
        Route::resource('Students', StudentController::class)->except(['show']);

        Route::get('Student/API', [StudentController::class, 'api'])->name('student.api');
        Route::get('Courses/API', [CourseController::class, 'apiName'])->name('course.apiName');
    });
