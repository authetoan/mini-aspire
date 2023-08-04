<?php

namespace App\Domain\Entities\User;

use Illuminate\Database\Eloquent\Model;

interface IUserRepository
{
    public function find(int $id): ?Model;

    public function findOneByEmailAndPassword(string $email, string $password): ?User;
}
