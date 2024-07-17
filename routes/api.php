<?php

use App\Http\Controllers\CategoryController;
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
        Route::get('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('reset-password', [UserController::class, 'reset']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('notes')->group(function () {
            Route::get('/', [NotesController::class, 'index']);
            Route::post('/', [NotesController::class, 'store']);
            Route::get('detail/{id}', [NotesController::class, 'detail']);
            Route::post('update/{id}', [NotesController::class, 'update']);
            Route::delete('delete/{id}', [NotesController::class, 'destroy']);
            Route::post('/search', [NotesController::class, 'search']);
            Route::get('/favorite/{id}', [NotesController::class, 'favorite']);
            Route::get('/favorite/', [NotesController::class, 'favorites']);
        });
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('category')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/{id}', [CategoryController::class, 'create']);
            Route::post('update/{id}', [CategoryController::class, 'update']);
            Route::delete('delete/{id}', [CategoryController::class, 'delete']);

        });
    });
});
