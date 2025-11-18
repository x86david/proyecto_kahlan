<?php

use App\Application\Service\UserService;
use App\Domain\Entity\User;
use App\Infrastructure\Persistence\DatabaseConnection;
use App\Infrastructure\Persistence\UserDatabaseRepository;

describe("UserService", function () {

    beforeEach(function () {
        $db = new DatabaseConnection();
        $repo = new UserDatabaseRepository($db);
        $this->service = new UserService($repo);
    });

    it("debería registrar un usuario válido", function () {
        $user = new User(3, "Lucía");
        $this->service->registerUser($user);

        $found = $this->service->listUsers();
        expect($found)->toContainKey(2); // índice 2 porque ya había 2 usuarios iniciales
        expect($found[2]->getNombre())->toBe("Lucía");
    });

    it("debería lanzar excepción si el ID < 1", function () {
        $user = new User(0, "Prueba");

        expect(function () use ($user) {
            $this->service->registerUser($user);
        })->toThrow(new DomainException("El ID del usuario debe ser mayor que 0"));
    });

    it("debería lanzar excepción si el nombre está vacío", function () {
        $user = new User(2, "");

        expect(function () use ($user) {
            $this->service->registerUser($user);
        })->toThrow(new DomainException("El nombre no puede estar vacío"));
    });

    it("debería listar usuarios existentes", function () {
        $users = $this->service->listUsers();

        expect($users)->toBeAn("array");
        expect($users[0]->getNombre())->toBe("Carlos");
        expect($users[1]->getNombre())->toBe("Ana");
    });

    it("debería borrar un usuario por ID", function () {
        $this->service->deleteUser(1);

        $users = $this->service->listUsers();
        $ids = array_map(fn($u) => $u->getId(), $users);

        expect($ids)->not->toContain(1);
    });
});
