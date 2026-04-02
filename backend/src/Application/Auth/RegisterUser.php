<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Core\ValidationException;
use App\Domain\Auth\SessionRepository;
use App\Domain\Auth\UserRepository;

final class RegisterUser
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly SessionRepository $sessions
    ) {
    }

    /**
     * @return array{token:string,user:array{id:int,name:string,email:string}}
     */
    public function execute(string $name, string $email, string $password): array
    {
        if ($name === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false || strlen($password) < 8) {
            throw new ValidationException('auth.invalid_registration_data');
        }

        if ($this->users->findByEmail($email) !== null) {
            throw new ValidationException('auth.email_exists');
        }

        $user = $this->users->create($name, $email, password_hash($password, PASSWORD_BCRYPT));
        $token = $this->sessions->create($user->id);

        return [
            'token' => $token,
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ];
    }
}
