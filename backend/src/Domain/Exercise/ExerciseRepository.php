<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

interface ExerciseRepository
{
    public function create(Exercise $exercise): int;
}
