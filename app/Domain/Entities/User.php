<?php

namespace App\Domain\Entities;

class User
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $email,
        private string $passwordHash
    ) {}

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): string { return $this->passwordHash; }

    public function setId(int $id): void { $this->id = $id; }
}
