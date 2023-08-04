<?php
declare(strict_types=1);

namespace App\Domain\Entities\Loan;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ILoanRepository
{
    public function update(int $id, array $updateData): void;

    public function save(Loan $loan): void;

    public function find(int $id): ?Model;

    public function findManyBy(array $criteria): Collection;

    public function getScheduledRepayment(int $loanId, int $scheduledRepaymentId): ?ScheduledRepayment;
}
