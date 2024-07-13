<?php

use App\Http\Controllers\NotesController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::prefix('auth')->group(function () {
        Route::post('login', [UserController::class, 'login'])->name('login');
        Route::post('register', [UserController::class, 'register']);
        Route::post('logout', [UserController::class, 'logout']);
    });

    Route::prefix('notes')->group(function () {
        Route::get('/', [NotesController::class, 'index']);
    });
});
