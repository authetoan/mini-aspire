<?php
declare(strict_types=1);

namespace App\Domain\DTOs\Requests\Loan;

class Store
{
    public function __construct(
        public float $amount,
        public int $term,
        public string $requestDate,
        public int $customerId,
    ) {
    }
}
