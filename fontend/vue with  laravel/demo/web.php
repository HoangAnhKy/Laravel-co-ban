<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn() => view('User'));

Route::group(['prefix' => 'api'], function () {
    Route::get('/user', [UserController::class, 'dataList'])->name('user.data-list');
    Route::any('/user-add', [UserController::class, 'addUser'])->name('user.add');
    Route::any('/user-edit/{id?}', [UserController::class, 'editUser'])->name('user.edit');
    Route::any('/user-delete/{id?}', [UserController::class, 'deleteUser'])->name('user.delete');
});