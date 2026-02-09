<?php

use App\Http\Controllers\API\BookController;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('books', controller:BookController::class);
// Route::get('/ping', function () {
//     return response()->json([
//         'message' => BookResource::collection(Book::all()),
//     ]);
// });

