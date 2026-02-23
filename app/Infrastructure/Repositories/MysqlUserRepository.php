<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepositoryInterface;
use PDO;

class MysqlUserRepository implements UserRepositoryInterface
{
    public function __construct(private PDO $db) {}

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM Usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return new User($row['id'], $row['nome'], $row['email'], $row['senha_hash']);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM Usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if (!$row) return null;

        return new User($row['id'], $row['nome'], $row['email'], $row['senha_hash']);
    }

    public function save(User $user): void
    {
        if ($user->getId()) {
            $stmt = $this->db->prepare("UPDATE Usuarios SET nome = ?, email = ?, senha_hash = ? WHERE id = ?");
            $stmt->execute([$user->getName(), $user->getEmail(), $user->getPasswordHash(), $user->getId()]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO Usuarios (nome, email, senha_hash) VALUES (?, ?, ?)");
            $stmt->execute([$user->getName(), $user->getEmail(), $user->getPasswordHash()]);
            $user->setId((int)$this->db->lastInsertId());
        }
    }
}
