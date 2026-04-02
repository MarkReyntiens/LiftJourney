<?php

declare(strict_types=1);

namespace App\Domain\Auth;

interface UserRepository
{
    public function create(string $name, string $email, string $passwordHash): User;

    public function findByEmail(string $email): ?array;
}
