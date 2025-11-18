<?php
namespace App\Infrastructure\Persistence;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;

class UserDatabaseRepository implements UserRepository {
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    public function findById(int $id): ?User {
        $row = $this->db->query($id);
        return $row ? new User($row['id'], $row['nombre']) : null;
    }

    public function findAll(): array {
        $rows = $this->db->queryArray("SELECT * FROM users");
        return array_map(fn($row) => new User($row['id'], $row['nombre']), $rows);
    }

    public function save(User $user): void {
        $this->db->insertOrUpdate($user);
    }

    public function deleteById(int $id): void {
        $this->db->deleteById($id);
    }
}
