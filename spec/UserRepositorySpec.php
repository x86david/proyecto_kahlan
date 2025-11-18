<?php

use App\Domain\Entity\User;
use App\Infrastructure\Persistence\DatabaseConnection;
use App\Infrastructure\Persistence\UserDatabaseRepository;

describe("UserDatabaseRepository", function () {

    beforeEach(function () {
        $this->dbConnection = new DatabaseConnection();
        $this->userRepo = new UserDatabaseRepository($this->dbConnection);
    });

    it("debería devolver un User existente por ID", function () {
        $user = $this->userRepo->findById(1);

        expect($user)->toBeAnInstanceOf(User::class);
        expect($user->getNombre())->toBe("Carlos");
    });

    it("debería devolver null si el ID no existe", function () {
        $user = $this->userRepo->findById(999);

        expect($user)->toBe(null);
    });

    it("debería devolver una lista de Users en findAll", function () {
        $users = $this->userRepo->findAll();

        expect($users)->toBeAn("array");
        expect($users[0])->toBeAnInstanceOf(User::class);
        expect($users[1])->toBeAnInstanceOf(User::class);
        expect($users[0]->getNombre())->toBe("Carlos");
        expect($users[1]->getNombre())->toBe("Ana");
    });

    it("debería guardar un nuevo User y luego recuperarlo", function () {
        $nuevo = new User(3, "Lucía");
        $this->userRepo->save($nuevo);

        $found = $this->userRepo->findById(3);
        expect($found)->toBeAnInstanceOf(User::class);
        expect($found->getNombre())->toBe("Lucía");
    });

    it("debería actualizar un User existente", function () {
        $update = new User(2, "Ana María");
        $this->userRepo->save($update);

        $found = $this->userRepo->findById(2);
        expect($found->getNombre())->toBe("Ana María");
    });

    it("debería borrar un User por ID", function () {
        $this->userRepo->deleteById(1);

        $deleted = $this->userRepo->findById(1);
        expect($deleted)->toBe(null);
    });
});
