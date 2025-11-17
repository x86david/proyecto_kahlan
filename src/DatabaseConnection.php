<?php
namespace App;

use App\Entity\User;

class DatabaseConnection {
    private array $data = [
        ['id' => 1, 'nombre' => 'Carlos'],
        ['id' => 2, 'nombre' => 'Ana'],
        ['id' => 3, 'nombre' => 'Luis']
    ];

    public function queryArray(string $sql): array {
        return $this->data;
    }

    public function query(int $id): ?array {
        foreach ($this->data as $row) {
            if ($row['id'] === $id) {
                return $row;
            }
        }
        return null;
    }

    public function insertOrUpdate(User $user): void {
        // Buscar si ya existe el usuario por ID
        foreach ($this->data as &$row) {
            if ($row['id'] === $user->getId()) {
                // Actualizar nombre
                $row['nombre'] = $user->getNombre();
                return;
            }
        }
        // Si no existe, insertar nuevo
        $this->data[] = [
            'id' => $user->getId(),
            'nombre' => $user->getNombre()
        ];
    }

    // Método auxiliar para ver los datos actuales (opcional)
    public function getData(): array {
        return $this->data;
    }
}
