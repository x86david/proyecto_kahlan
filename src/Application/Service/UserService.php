<?php
namespace App\Application\Service;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepository;

class UserService {
    private UserRepository $repo;

    public function __construct(UserRepository $repo) {
        $this->repo = $repo;
    }

    public function registerUser(User $user): void {
        if ($user->getId() < 1) {
            throw new \DomainException("El ID del usuario debe ser mayor que 0");
        }

        if (empty($user->getNombre())) {
            throw new \DomainException("El nombre no puede estar vacío");
        }

        $this->repo->save($user);
    }

    public function listUsers(): array {
        return $this->repo->findAll();
    }

    public function deleteUser(int $id): void {
        $this->repo->deleteById($id);
    }
}
