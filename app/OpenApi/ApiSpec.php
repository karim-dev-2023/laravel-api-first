<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: "Library API",
        version: "1.0.0",
        description: "API d'authentification et gestion de livres (Laravel + Sanctum)."
    ),
    servers: [
        new OA\Server(
            url: "http://localhost:8000/api/v2",
            description: "Local"
        ),
    ]
)]
class OpenApi
{
}
