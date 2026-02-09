<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return BookResource::collection(Book::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return Book::create([
            "title" => "Rimka",
            "author" => "Karim",
            "summary" => "Pourquoi faire du sport de combat ?",
            "isbn" => "9787234567890",
        ]);
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

        return response()->json($book, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        $book->delete();

        return response()->json("Suppression effectuée", 200);
    }
}
