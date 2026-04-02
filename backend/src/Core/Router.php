<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

final class Router
{
    /** @var array<string, array<string, array{handler: callable, middleware: array<int, callable>}>> */
    private array $routes = [];

    public function get(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $route = $this->routes[$method][$path] ?? null;

        if ($route === null) {
            jsonResponse(['message' => trans('error.not_found')], 404);
            return;
        }

        try {
            foreach ($route['middleware'] as $mw) {
                $mw();
            }
            $route['handler']();
        } catch (ValidationException $e) {
            jsonResponse(['message' => trans($e->getMessage())], 422);
        } catch (UnauthorizedException $e) {
            jsonResponse(['message' => trans($e->getMessage())], 401);
        } catch (Throwable $e) {
            jsonResponse(['message' => trans('error.server')], 500);
        }
    }

    private function addRoute(string $method, string $path, callable $handler, array $middleware): void
    {
        $this->routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }
}

final class ValidationException extends \RuntimeException
{
}

final class UnauthorizedException extends \RuntimeException
{
}

function requestJson(): array
{
    $raw = file_get_contents('php://input') ?: '';
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
}

function bearerToken(): ?string
{
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s+(.+)/', $header, $matches) !== 1) {
        return null;
    }
    return trim($matches[1]);
}

function trans(string $key): string
{
    return I18n::t($key);
}
