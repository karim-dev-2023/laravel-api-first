<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;


class BookController extends Controller
{

    #[OA\Get(
        path: "/api/v2/books",
        description: "Liste des livres paginée",
        responses: [
            new OA\Response(
                response: 200,
                description: "Succès"
            ),
            new OA\Response(
                response: 404,
                description: "Aucun livre trouvé"
            )
        ]
    )]
    public function index()
    {
        $books = Book::paginate(2);

        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'Aucun livre trouvé'
            ], 404);
        }

        return response()->json($books, 200);
    }



    #[OA\Post(
        path: "/api/v2/books",
        description: "Création d'un livre",
        parameters: [
            new OA\Parameter(
                name: "Authorization",
                description: "Token Bearer",
                in: "header",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "Bearer 1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
                )
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["title", "author", "summary", "isbn"],
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Livre créé",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "data" => [
                            "id" => 1,
                            "title" => "Rimka",
                            "author" => "Karim",
                            "summary" => "Pourquoi faire du sport de combat ?",
                            "isbn" => "9787234567890",
                        ]
                    ]
                )
            ),

            new OA\Response(
                response: 500,
                description: "Erreur serveur",
                content: new OA\JsonContent(
                    type: "object",
                    example: ["message" => "Erreur interne du serveur"]
                )
            ),
        ]
    )]
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string'],
            'isbn' => ['required', 'string', 'max:20'],
        ]);

        try {

            $book = Book::create($validated);


            return response()->json(new BookResource($book), 201);
        } catch (\Throwable $e) {

            return response()->json([
                'message' => "Erreur interne du serveur"
            ], 500);
        }
    }


    #[OA\Get(
        path: "/api/v2/books/{id}",
        description: "Informations d'un livre",
        parameters: [
            new OA\Parameter(
                name: "Authorization",
                description: "Token Bearer",
                in: "header",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "Bearer 1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
                )
            ),
            new OA\Parameter(
                name: "id",
                description: "ID du livre ",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 2)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Livre trouvé",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "data" => [
                            "title" => "Rimka_Update_Update_Update_Update",
                            "author" => "KARIM",
                            "summary" => "Pourquoi faire du sport de combat ?",
                            "isbn" => "9787234567890",
                            "_links" => [
                                "self" => "http://localhost:8000/api/v2/books/12",
                                "update" => "http://localhost:8000/api/v2/books/12",
                                "delete" => "http://localhost:8000/api/v2/books/12",
                                "all" => "http://localhost:8000/api/v2/books"
                            ]
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Livre introuvable",
                content: new OA\JsonContent(
                    type: "object",
                    example: ["message" => "Livre introuvable"]
                )
            ),
        ]
    )]
    public function show(string $id)
    {
        // Mise en place de la mise en cache
        return Cache::remember("book-{$id}", 3600, function () use ($id) {

            $book = Book::findOrFail($id);

            if (!$book) {
                return response()->json(['message' => 'Livre introuvable'], 404);
            }

            return response()->json(new BookResource($book), 200);
        });
    }

    #[OA\Put(
        path: "/api/v2/books/{id}",
        description: "Mise à jour d'un livre",
        parameters: [
            new OA\Parameter(
                name: "Authorization",
                description: "Token Bearer",
                in: "header",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "Bearer 1|eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
                )
            ),
            new OA\Parameter(
                name: "id",
                description: "ID du livre a mettre à jour",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 12)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Mise à jour réussie",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "title" => "Rimka_Update_Update_Update",
                        "author" => "KARIM",
                        "summary" => "Pourquoi faire du sport de combat ?",
                        "isbn" => "9787234567890",
                        "_links" => [
                            "self" => "http://localhost:8000/api/v2/books/12",
                            "update" => "http://localhost:8000/api/v2/books/12",
                            "delete" => "http://localhost:8000/api/v2/books/12",
                            "all" => "http://localhost:8000/api/v2/books"
                        ]
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Livre introuvable",
                content: new OA\JsonContent(
                    type: "object",
                    example: ["message" => "Livre introuvable"]
                )
            ),
        ]
    )]
    public function update(Request $request, string $id): JsonResponse
    {
        $book = Book::find($id);

        $book->update([
            'title' => $book->title . '_Update'
        ]);
        if (!$book) {
            return response()->json(['message' => 'Livre introuvable'], 404);
        }
        Cache::forget("book-{$id}");
        return response()->json(new BookResource($book), 200);
    }


    #[OA\Delete(
        path: "/api/v2/books/{id}",
        description: "Suppression d'un livre",
        parameters: [
            new OA\Parameter(
                name: "Authorization",
                description: "Token Bearer",
                in: "header",
                required: true,
                schema: new OA\Schema(
                    type: "string",
                    example: "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
                )
            ),
            new OA\Parameter(
                name: "id",
                description: "ID du livre à supprimer",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer", example: 12)
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Livre supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Livre introuvable",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "message" => "Livre introuvable"
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Erreur serveur",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "message" => "Erreur interne du serveur"
                    ]
                )
            ),
        ]
    )]
    public function destroy(int $id)
    {
        try {
            $book = Book::find($id);

            if (!$book) {
                return response()->json([
                    'message' => 'Livre introuvable'
                ], 404);
            }

            $book->delete();

            Cache::forget("book-{$id}");

            return response()->json([
                'message' => "Livre supprimé avec succès"
            ], 204);
            return response()->noContent();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }
}
