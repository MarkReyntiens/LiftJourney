<?php

declare(strict_types=1);

namespace App\Domain\Exercise;

interface MuscleGroupRepository
{
    /** @return array<int, array{id:int,name:string}> */
    public function all(): array;

    /** @param array<int> $ids */
    public function existAll(array $ids): bool;
}
