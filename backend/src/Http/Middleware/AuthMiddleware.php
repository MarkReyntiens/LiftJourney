<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Core\UnauthorizedException;
use App\Domain\Auth\SessionRepository;
use function App\Core\bearerToken;

final class AuthMiddleware
{
    public function __construct(private readonly SessionRepository $sessions)
    {
    }

    public function __invoke(): void
    {
        $token = bearerToken();
        if ($token === null) {
            throw new UnauthorizedException('auth.token_missing');
        }

        $userId = $this->sessions->findUserIdByToken($token);
        if ($userId === null) {
            throw new UnauthorizedException('auth.token_invalid_or_expired');
        }

        $_SERVER['AUTH_USER_ID'] = (string) $userId;
    }
}
