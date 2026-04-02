<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Auth\LoginUser;
use App\Application\Auth\RegisterUser;
use function App\Core\jsonResponse;
use function App\Core\requestJson;

final class AuthController
{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly LoginUser $loginUser
    ) {
    }

    public function register(): void
    {
        $data = requestJson();
        $result = $this->registerUser->execute(
            trim((string) ($data['name'] ?? '')),
            trim((string) ($data['email'] ?? '')),
            (string) ($data['password'] ?? '')
        );

        jsonResponse($result, 201);
    }

    public function login(): void
    {
        $data = requestJson();
        $result = $this->loginUser->execute(
            trim((string) ($data['email'] ?? '')),
            (string) ($data['password'] ?? '')
        );

        jsonResponse($result, 200);
    }
}
