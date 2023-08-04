<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Database\Eloquent\Repositories;

use App\Domain\Entities\Loan\ILoanRepository;
use App\Domain\Entities\Loan\Loan;
use App\Domain\Entities\Loan\ScheduledRepayment;

class EloquentLoanRepository extends BaseRepository implements ILoanRepository
{
    public function __construct()
    {
        $this->setModel(new Loan());
    }

    public function save(Loan $loan): void
    {
        $loan->customer_id = $loan->getCustomer()->getId();
        unset($loan->customer);
        $loan->save();
        if ($loan->getScheduledRepayments()) {
            foreach ($loan->getScheduledRepayments() as $scheduledRepayment) {
                $scheduledRepayment->loan_id = $loan->getId();
                unset($scheduledRepayment->loan);
                $scheduledRepayment->save();
            }
        }
    }

    public function getScheduledRepayment(int $loanId, int $scheduledRepaymentId): ?ScheduledRepayment
    {
        return ScheduledRepayment::where('loan_id', $loanId)->where('id', $scheduledRepaymentId)->first();
    }
}
