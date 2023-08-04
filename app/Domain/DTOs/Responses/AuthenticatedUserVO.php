<?php
declare(strict_types=1);

namespace App\Domain\DTOs\Responses;

class AuthenticatedUserVO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $token,
    ) {
    }
}
