<?php

declare(strict_types=1);

namespace App\Application\Exercise;

use App\Core\ValidationException;
use App\Domain\Exercise\Exercise;
use App\Domain\Exercise\ExerciseRepository;
use App\Domain\Exercise\MuscleGroupRepository;

final class CreateExercise
{
    public function __construct(
        private readonly ExerciseRepository $exercises,
        private readonly MuscleGroupRepository $muscleGroups
    ) {
    }

    /**
     * @param array<int> $muscleGroupIds
     */
    public function execute(
        int $userId,
        string $name,
        string $description,
        string $imageUrl,
        array $muscleGroupIds,
        int $setsCount,
        int $startReps,
        ?float $startWeightKg
    ): int {
        if ($name === '' || $description === '' || $imageUrl === '') {
            throw new ValidationException('exercise.required_fields');
        }
        if ($setsCount < 1 || $startReps < 1) {
            throw new ValidationException('exercise.invalid_sets_or_reps');
        }
        if ($muscleGroupIds === [] || $this->muscleGroups->existAll($muscleGroupIds) === false) {
            throw new ValidationException('exercise.invalid_muscle_groups');
        }
        if ($startWeightKg !== null && $startWeightKg < 0) {
            throw new ValidationException('exercise.invalid_start_weight');
        }

        $exercise = new Exercise(
            $userId,
            $name,
            $description,
            $imageUrl,
            $muscleGroupIds,
            $setsCount,
            $startReps,
            $startWeightKg
        );

        return $this->exercises->create($exercise);
    }
}
