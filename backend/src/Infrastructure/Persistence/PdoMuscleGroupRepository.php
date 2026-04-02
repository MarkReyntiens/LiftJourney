<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Exercise\MuscleGroupRepository;
use PDO;

final class PdoMuscleGroupRepository implements MuscleGroupRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, name FROM muscle_groups ORDER BY name');
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    public function existAll(array $ids): bool
    {
        $ids = array_values(array_unique(array_map(static fn ($id): int => (int) $id, $ids)));
        if ($ids === []) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS c FROM muscle_groups WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $count = (int) $stmt->fetchColumn();

        return $count === count($ids);
    }
}
