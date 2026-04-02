<?php

declare(strict_types=1);

function sendCorsHeaders(): void
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Locale');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
}

function normalizeApiPath(string $path): string
{
    if (str_starts_with($path, '/index.php/')) {
        $path = substr($path, strlen('/index.php'));
    } elseif ($path === '/index.php') {
        $path = '/';
    }

    return $path;
}

function healthLoadEnv(string $path): void
{
    if (file_exists($path) === false) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
        $key = trim($key);
        if ($key === '') {
            continue;
        }

        $_ENV[$key] = trim($value);
    }
}

/**
 * @return array{status:string,error:?string,hint:?string}
 */
function healthCheckDatabase(): array
{
    try {
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '5432';
        $db = $_ENV['DB_NAME'] ?? '';
        $user = $_ENV['DB_USER'] ?? '';
        $pass = $_ENV['DB_PASS'] ?? '';
        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $db);

        $pdo = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_TIMEOUT => 3,
        ]);

        $pdo->query('SELECT 1');
        return ['status' => 'ok', 'error' => null, 'hint' => null];
    } catch (\Throwable $e) {
        return [
            'status' => 'error',
            'error' => 'connection_failed',
            'hint' => healthDatabaseHint($e->getMessage()),
        ];
    }
}

function healthDatabaseHint(string $message): string
{
    $msg = strtolower($message);

    if (str_contains($msg, 'unknown database') || str_contains($msg, 'database "') && str_contains($msg, '" does not exist')) {
        return 'unknown_database_name';
    }
    if (str_contains($msg, 'access denied for user')) {
        return 'invalid_db_user_or_password';
    }
    if (str_contains($msg, 'password authentication failed')) {
        return 'invalid_db_user_or_password';
    }
    if (str_contains($msg, 'php_network_getaddresses') || str_contains($msg, 'name or service not known')) {
        return 'invalid_db_host';
    }
    if (str_contains($msg, 'connection refused') || str_contains($msg, 'timed out')) {
        return 'db_host_unreachable';
    }

    return 'check_db_host_name_user_password';
}

sendCorsHeaders();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Health check without DB/bootstrap dependency.
$incomingPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$incomingPath = normalizeApiPath($incomingPath);
$incomingScriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($incomingScriptDir !== '' && $incomingScriptDir !== '/' && str_starts_with($incomingPath, $incomingScriptDir . '/')) {
    $incomingPath = substr($incomingPath, strlen($incomingScriptDir));
    if ($incomingPath === '') {
        $incomingPath = '/';
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && $incomingPath === '/api/health') {
    healthLoadEnv(__DIR__ . '/.env');
    $database = healthCheckDatabase();
    $overallStatus = $database['status'] === 'ok' ? 'ok' : 'degraded';

    header('Content-Type: application/json');
    http_response_code($overallStatus === 'ok' ? 200 : 503);
    echo json_encode([
        'status' => $overallStatus,
        'service' => 'liftjourney-api',
        'timestamp' => gmdate(DATE_ATOM),
        'database' => $database,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/src/bootstrap.php';

use App\Core\I18n;
use App\Core\Router;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\OptionsController;
use App\Http\Middleware\AuthMiddleware;

I18n::setLocale(I18n::detectLocaleFromRequest());

$router = new Router();

$authController = container(AuthController::class);
$exerciseController = container(ExerciseController::class);
$optionsController = container(OptionsController::class);
$authMiddleware = container(AuthMiddleware::class);

$router->post('/api/auth/register', [$authController, 'register']);
$router->post('/api/auth/login', [$authController, 'login']);

$router->get('/api/options', [$optionsController, 'index'], [$authMiddleware]);
$router->get('/api/muscle-groups', [$exerciseController, 'muscleGroups'], [$authMiddleware]);
$router->post('/api/exercises', [$exerciseController, 'store'], [$authMiddleware]);

$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestPath = normalizeApiPath($requestPath);
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
if ($scriptDir !== '' && $scriptDir !== '/' && str_starts_with($requestPath, $scriptDir . '/')) {
    $requestPath = substr($requestPath, strlen($scriptDir));
    if ($requestPath === '') {
        $requestPath = '/';
    }
}

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $requestPath);
