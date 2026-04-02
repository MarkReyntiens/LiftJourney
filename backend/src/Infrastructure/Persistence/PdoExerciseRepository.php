<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Exercise\Exercise;
use App\Domain\Exercise\ExerciseRepository;
use PDO;

final class PdoExerciseRepository implements ExerciseRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function create(Exercise $exercise): int
    {
        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare(
            'INSERT INTO exercises (user_id, name, description, image_url, sets_count, start_reps, start_weight_kg)
             VALUES (:user_id, :name, :description, :image_url, :sets_count, :start_reps, :start_weight_kg)'
        );
        $stmt->execute([
            'user_id' => $exercise->userId,
            'name' => $exercise->name,
            'description' => $exercise->description,
            'image_url' => $exercise->imageUrl,
            'sets_count' => $exercise->setsCount,
            'start_reps' => $exercise->startReps,
            'start_weight_kg' => $exercise->startWeightKg,
        ]);

        $exerciseId = (int) $this->pdo->lastInsertId();

        $linkStmt = $this->pdo->prepare(
            'INSERT INTO exercise_target_muscles (exercise_id, muscle_group_id) VALUES (:exercise_id, :muscle_group_id)'
        );

        foreach ($exercise->muscleGroupIds as $muscleId) {
            $linkStmt->execute([
                'exercise_id' => $exerciseId,
                'muscle_group_id' => $muscleId,
            ]);
        }

        $this->pdo->commit();

        return $exerciseId;
    }
}
