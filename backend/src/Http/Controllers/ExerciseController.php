<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Application\Exercise\CreateExercise;
use App\Domain\Exercise\MuscleGroupRepository;
use function App\Core\jsonResponse;
use function App\Core\requestJson;

final class ExerciseController
{
    public function __construct(
        private readonly CreateExercise $createExercise,
        private readonly MuscleGroupRepository $muscleGroups
    ) {
    }

    public function muscleGroups(): void
    {
        jsonResponse(['muscleGroups' => $this->muscleGroups->all()]);
    }

    public function store(): void
    {
        $data = requestJson();
        $userId = (int) ($_SERVER['AUTH_USER_ID'] ?? 0);

        $id = $this->createExercise->execute(
            $userId,
            trim((string) ($data['name'] ?? '')),
            trim((string) ($data['description'] ?? '')),
            trim((string) ($data['imageUrl'] ?? '')),
            array_map(static fn ($item): int => (int) $item, (array) ($data['muscleGroupIds'] ?? [])),
            (int) ($data['setsCount'] ?? 0),
            (int) ($data['startReps'] ?? 0),
            isset($data['startWeightKg']) && $data['startWeightKg'] !== '' ? (float) $data['startWeightKg'] : null
        );

        jsonResponse(['id' => $id], 201);
    }
}
