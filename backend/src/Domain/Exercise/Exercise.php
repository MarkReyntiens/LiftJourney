<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

final class Exercise
{
    /**
     * @param array<int> $muscleGroupIds
     */
    public function __construct(
        public readonly int $userId,
        public readonly string $name,
        public readonly string $description,
        public readonly string $imageUrl,
        public readonly array $muscleGroupIds,
        public readonly int $setsCount,
        public readonly int $startReps,
        public readonly ?float $startWeightKg
    ) {
    }
}
