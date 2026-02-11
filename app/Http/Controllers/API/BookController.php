<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mise en place de la mise en cache
        return Cache::remember('books-list', 60, function () {
            return BookResource::collection(
                Book::paginate(2) //Mise en place de la paginations
            );
        });
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $book = Book::create([
            "title" => "Rimka",
            "author" => "Karim",
            "summary" => "Pourquoi faire du sport de combat ?",
            "isbn" => "9787234567890",
        ]);

        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return new BookResource(Book::find($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);

        $book->update([
            'title' => $book->title . '_Update'
        ]);

        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        $book->delete();

        return response()->noContent();
    }
}
