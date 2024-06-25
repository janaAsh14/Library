<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\MuserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::middleware('jwt.verify')->group(function () {

    Route::get('/book/{title}', [BookController::class, 'getBooks']);
    Route::post('/book-order', [BookController::class, 'orderBook']);

    Route::middleware('manager')->group(function () {
        Route::post('/data-entry', [MuserController::class, 'addDataEntry']);
        Route::post('/book', [BookController::class, 'addBook']);
        Route::delete('/book/{id}', [BookController::class, 'deleteBook']);
        Route::put('/book/{id}', [BookController::class, 'editBook']);
    });

    Route::middleware('data-entry')->group(function () {
        Route::post('/book', [BookController::class, 'addBook']);
        Route::delete('/book/{id}', [BookController::class, 'deleteBook']);
        Route::put('/book/{id}', [BookController::class, 'editBook']);
    });
});
