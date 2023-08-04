<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Database\Eloquent\Repositories;

use App\Domain\Entities\User\IUserRepository;
use App\Domain\Entities\User\User;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository extends BaseRepository implements IUserRepository
{

    public function __construct()
    {
        $this->setModel(new User());
    }

    public function findOneByEmailAndPassword(string $email, string $password): ?User
    {
        $user = $this->model->where('email', $email)->first();
        if (!$user) {
            return null;
        }

        if (!Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }
}
