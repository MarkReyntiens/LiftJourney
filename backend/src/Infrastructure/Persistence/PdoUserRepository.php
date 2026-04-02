<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use PDO;

final class PdoUserRepository implements UserRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(string $name, string $email, string $passwordHash): User
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return new User($id, $name, $email);
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return is_array($row) ? $row : null;
    }
}
