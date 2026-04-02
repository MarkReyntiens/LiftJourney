<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix) === false) {
        return;
    }

    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $path = __DIR__ . DIRECTORY_SEPARATOR . $relative . '.php';

    if (file_exists($path)) {
        require_once $path;
    }
});

use App\Application\Auth\LoginUser;
use App\Application\Auth\RegisterUser;
use App\Application\Exercise\CreateExercise;
use App\Core\Database;
use App\Domain\Auth\SessionRepository;
use App\Domain\Auth\UserRepository;
use App\Domain\Exercise\ExerciseRepository;
use App\Domain\Exercise\MuscleGroupRepository;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\OptionsController;
use App\Http\Middleware\AuthMiddleware;
use App\Infrastructure\Persistence\PdoExerciseRepository;
use App\Infrastructure\Persistence\PdoMuscleGroupRepository;
use App\Infrastructure\Persistence\PdoSessionRepository;
use App\Infrastructure\Persistence\PdoUserRepository;

loadEnv(__DIR__ . '/../.env');

$pdo = Database::createPdoFromEnv();

/** @var array<string, object> $container */
$container = [];

$container[UserRepository::class] = new PdoUserRepository($pdo);
$container[SessionRepository::class] = new PdoSessionRepository($pdo);
$container[MuscleGroupRepository::class] = new PdoMuscleGroupRepository($pdo);
$container[ExerciseRepository::class] = new PdoExerciseRepository($pdo);

$container[RegisterUser::class] = new RegisterUser(
    $container[UserRepository::class],
    $container[SessionRepository::class]
);
$container[LoginUser::class] = new LoginUser(
    $container[UserRepository::class],
    $container[SessionRepository::class]
);
$container[CreateExercise::class] = new CreateExercise(
    $container[ExerciseRepository::class],
    $container[MuscleGroupRepository::class]
);

$container[AuthController::class] = new AuthController(
    $container[RegisterUser::class],
    $container[LoginUser::class]
);
$container[ExerciseController::class] = new ExerciseController(
    $container[CreateExercise::class],
    $container[MuscleGroupRepository::class]
);
$container[OptionsController::class] = new OptionsController();
$container[AuthMiddleware::class] = new AuthMiddleware($container[SessionRepository::class]);

function container(string $id): object
{
    global $container;
    if (isset($container[$id]) === false) {
        throw new RuntimeException('Service not found: ' . $id);
    }

    return $container[$id];
}

function loadEnv(string $path): void
{
    if (file_exists($path) === false) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key = trim($key);
        $value = trim($value);
        if ($key === '') {
            continue;
        }
        $_ENV[$key] = $value;
    }
}
