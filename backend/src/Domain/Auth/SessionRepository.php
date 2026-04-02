<?php

declare(strict_types=1);

namespace App\Domain\Auth;

interface SessionRepository
{
    public function create(int $userId): string;

    public function findUserIdByToken(string $token): ?int;
}
