<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Auth\SessionRepository;
use PDO;

final class PdoSessionRepository implements SessionRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(int $userId): string
    {
        $token = bin2hex(random_bytes(32));

        $stmt = $this->pdo->prepare(
            "INSERT INTO user_sessions (user_id, token, expires_at) VALUES (:user_id, :token, NOW() + INTERVAL '30 days')"
        );
        $stmt->execute([
            'user_id' => $userId,
            'token' => $token,
        ]);

        return $token;
    }

    public function findUserIdByToken(string $token): ?int
    {
        $stmt = $this->pdo->prepare(
            'SELECT user_id FROM user_sessions WHERE token = :token AND expires_at > NOW() LIMIT 1'
        );
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        return is_array($row) ? (int) $row['user_id'] : null;
    }
}
