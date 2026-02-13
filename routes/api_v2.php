<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BookController;
use Illuminate\Support\Facades\Route;

  
Route::post('/register', [UserController::class, 'register']);
// Limitation du nb de requete de 10 par minutes
Route::post('/login', [UserController::class, 'login'])->middleware('throttle:5,1');


// Accéder uniquement si l'utilisateur est authentifié
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/books', [BookController::class, 'store']);

    Route::match(['put', 'patch'], '/books/{book}', [BookController::class, 'update'])->name('books.update');

    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.remove');
});



Route::get('/books', [BookController::class, 'index'])->name('books.list');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.detail');
