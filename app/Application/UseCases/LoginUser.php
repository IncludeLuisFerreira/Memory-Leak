<?php

namespace App\Application\UseCases;

use App\Domain\Repositories\UserRepositoryInterface;
use App\Domain\Entities\User;

class LoginUser
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function execute(string $email, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user && password_verify($password, $user->getPasswordHash())) {
            return $user;
        }
        return null;
    }
}
