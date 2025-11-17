<?php

use Kahlan\Plugin\Double;
use App\DatabaseConnection;
use App\Repository\UserDatabaseRepository;
use App\Entity\User;

describe("UserRepository", function () {

    beforeEach(function () {
        $this->dbConnection = Double::instance(['extends' => DatabaseConnection::class]);
        $this->userRepo = new UserDatabaseRepository($this->dbConnection);
    });

    it("Debería devolver un User cuando se encuentra por ID", function () {
        $user = $this->userRepo->findById(1);

        expect($user)->toBeAnInstanceOf(User::class);
        expect($user->getNombre())->toBe('Carlos');
    });

    it("Debería devolver una lista de Users en findAll", function () {
        $users = $this->userRepo->findAll();

        expect($users)->toBeAn('array');
        expect($users[0])->toBeAnInstanceOf(User::class);
        expect($users[0]->getNombre())->toBe('Carlos');
        expect($users[1]->getNombre())->toBe('Ana');
    });

    it("Debería lanzar una excepción si el ID es negativo en save", function () {
        $userNegativo = new User(-1, "Prueba");

        expect(function () use ($userNegativo) {
            $this->userRepo->save($userNegativo);
        })->toThrow(new InvalidArgumentException("El ID del usuario no puede ser negativo"));
    });
    
});
