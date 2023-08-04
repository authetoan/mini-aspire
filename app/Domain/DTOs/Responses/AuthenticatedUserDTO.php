<?php
declare(strict_types=1);

namespace App\Domain\DTOs\Responses;

class AuthenticatedUserDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $token,
    ) {
    }
}
