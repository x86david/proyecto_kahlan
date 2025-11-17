<?php
namespace App\Repository;

use App\DatabaseConnection;
use App\Entity\User;

class UserDatabaseRepository implements UserRepository {
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function findById(int $id): ?User {
        $row = $this->db->query($id);
        return $row ? new User($row['id'], $row['nombre']) : null;
    }

    /** @return User[] */
    public function findAll(): array {
        $rows = $this->db->queryArray("SELECT * FROM users");
        return array_map(fn($row) => new User($row['id'], $row['nombre']), $rows);
    }

    public function save(User $user): void {
        if ($user->getId() < 0) {
            throw new \InvalidArgumentException("El ID del usuario no puede ser negativo");
        }

        $this->db->insertOrUpdate($user);
    }
}
