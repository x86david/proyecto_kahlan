<?php
namespace App\Infrastructure\Persistence;

use App\Domain\Entity\User;

class DatabaseConnection {
    private array $data = [
        ['id' => 1, 'nombre' => 'Carlos'],
        ['id' => 2, 'nombre' => 'Ana'],
    ];

    public function queryArray(string $sql): array {
        return $this->data;
    }

    public function query(int $id): ?array {
        foreach ($this->data as $row) {
            if ($row['id'] === $id) return $row;
        }
        return null;
    }

    public function insertOrUpdate(User $user): void {
        foreach ($this->data as &$row) {
            if ($row['id'] === $user->getId()) {
                $row['nombre'] = $user->getNombre();
                return;
            }
        }
        $this->data[] = ['id' => $user->getId(), 'nombre' => $user->getNombre()];
    }

    public function deleteById(int $id): void {
        foreach ($this->data as $key => $row) {
            if ($row['id'] === $id) {
                unset($this->data[$key]);
            }
        }
    }
}
