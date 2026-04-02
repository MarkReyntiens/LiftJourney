<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use function App\Core\jsonResponse;

final class OptionsController
{
    public function index(): void
    {
        jsonResponse([
            'options' => [
                ['key' => 'create-exercise'],
                ['key' => 'start-workout'],
                ['key' => 'history'],
                ['key' => 'profile'],
            ],
        ]);
    }
}
