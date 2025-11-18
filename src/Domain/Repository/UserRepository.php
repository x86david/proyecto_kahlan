<?php
namespace App\Domain\Repository;

use App\Domain\Entity\User;

interface UserRepository {
    public function findById(int $id): ?User;
    public function findAll(): array;
    public function save(User $user): void;
    public function deleteById(int $id): void;
}
