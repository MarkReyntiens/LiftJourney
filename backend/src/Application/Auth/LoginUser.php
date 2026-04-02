<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Core\UnauthorizedException;
use App\Core\ValidationException;
use App\Domain\Auth\SessionRepository;
use App\Domain\Auth\UserRepository;

final class LoginUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly SessionRepository $sessions
    ) {
    }

    /**
     * @return array{token:string,user:array{id:int,name:string,email:string}}
     */
    public function execute(string $email, string $password): array
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || $password === '') {
            throw new ValidationException('auth.invalid_login_data');
        }

        $rawUser = $this->users->findByEmail($email);
        if ($rawUser === null || password_verify($password, $rawUser['password_hash']) === false) {
            throw new UnauthorizedException('auth.invalid_credentials');
        }

        $token = $this->sessions->create((int) $rawUser['id']);

        return [
            'token' => $token,
            'user' => [
                'id' => (int) $rawUser['id'],
                'name' => (string) $rawUser['name'],
                'email' => (string) $rawUser['email'],
            ],
        ];
    }
}
