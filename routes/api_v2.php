<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Accéder uniquement si l'utilisateur est authentifié
// Limitation du nb de requete de 10 par minutes
Route::middleware('auth:sanctum','throttle:10,1')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/books', [BookController::class, 'store']);

    Route::match(['put', 'patch'], '/books/{book}', [BookController::class, 'update']);

    Route::delete('/books/{book}', [BookController::class, 'destroy']);
});


 
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);
