<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{

    #[OA\Post(
        path: "/api/v2/register",
        description: "Création d'un utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["name", "email", "password"],
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Utilisateur créer créé",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "name" => "Rimka",
                        "email" => "Karim@mail.com",
                        "password" => "9787234567890",
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
    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8',
        ]);
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            return response()->json([
                'message' => 'Utilisateur créé avec succès',
                'user' => $user
            ], 201);
        } catch (\Throwable $e) {

            return response()->json([
                'message' => "Erreur interne du serveur"
            ], 500);
        }
    }

    #[OA\Post(
        path: "/api/v2/login",
        description: "Connexion utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                required: ["email", "password"],
            )
        ),
        responses: [
            new OA\Response(
                response: 401,
                description: "Identifiants ou mot de passes invalides",
                content: new OA\JsonContent(
                    type: "object",
                    example: ["message" => "Identifiants invalides"]
                )
            ),
            new OA\Response(
                response: 200,
                description: "Utilisateur Connecté",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "email" => "Karim@mail.com",
                        "password" => "9787234567890",
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Identifiants invalides'
            ], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => $user,
        ], 200);
    }

    #[OA\Post(
        path: "/api/v2/logout",
        description: "Déconnexion utilisateur",
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
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Déconnexion réussie",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "message" => "Déconnexion réussie"
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Non authentifié",
                content: new OA\JsonContent(
                    type: "object",
                    example: [
                        "message" => "Unauthenticated."
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
            )
        ]
    )]
    public function logout(Request $request)
    {
        try {

            if (!$request->user()) {
                return response()->json([
                    'message' => 'Non authentifié'
                ], 401);
            }

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Déconnexion réussie'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }
}
