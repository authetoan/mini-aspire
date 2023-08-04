<?php
declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Entities\Loan\ILoanRepository;
use App\Domain\Entities\Loan\Loan;
use App\Domain\Entities\Loan\ScheduledRepayment;
use App\Domain\Entities\User\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LoanService
{
    public function __construct(
        private readonly ILoanRepository $loanRepository,
        private readonly AuthService $authService,
    ) {
    }

    /**
     * @throws Exception
     */
    public function storeLoan(Request $request): Loan
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'term' => 'required|integer|min:1',
            'request_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            throw new HttpException(422, $validator->errors()->first());
        }

        $user = $this->authService->getAuthenticatedUser();

        $loan = new Loan();
        $loan->setCustomer($user);
        $loan->setAmount($request->amount);
        $loan->setTerm($request->term);
        $loan->setStatus(Loan::STATUS_PENDING);
        $loan->setRequestDate(new \DateTime($request->request_date));

        $installmentAmount = $loan->getAmount() / $loan->getTerm();
        for ($i = 1; $i <= $loan->getTerm(); $i++) {
            $amount = ($i === $loan->getTerm()) ?
                ($loan->getAmount() - ($installmentAmount * ($loan->getTerm() - 1)))
                : $installmentAmount;
            $scheduledRepayment = new ScheduledRepayment();
            $scheduledRepayment->setLoan($loan);
            $scheduledRepayment->setStatus(ScheduledRepayment::STATUS_PENDING);
            $scheduledRepayment->setAmount($amount);
            $scheduledRepayment->setDueDate(Carbon::parse($loan->getRequestDate())->addWeeks($i));

            $loan->addScheduledRepayment($scheduledRepayment);
        }

        $this->loanRepository->save($loan);

        return $loan;
    }

    public function approve(User $user, int $loanId): void
    {
        /** @var Loan $loan */
        $loan = $this->loanRepository->find($loanId);
        if (!$loan) {
            throw new HttpException(404, 'Loan not found.');
        }

        if ($loan->getStatus() !== Loan::STATUS_PENDING) {
            throw new HttpException(422, 'Loan has already been approved.');
        }

        if (!$user->isAdmin()) {
            throw new HttpException(403, 'Only admins can approve loans.');
        }

        $this->loanRepository->update($loanId, [
            'status' => Loan::STATUS_APPROVED,
        ]);
    }

    public function getCustomerLoans(User $user): ?Collection
    {
        return $this->loanRepository->findManyBy([
            'customer_id' => $user->getId(),
        ]);
    }

    public function getCustomerLoan(User $user, int $loanId): Loan
    {
        /** @var Loan $loan */
        $loan = $this->loanRepository->find($loanId);
        if (!$loan) {
            throw new HttpException(404, 'Loan not found.');
        }

        if ($loan->getCustomer()->getId() !== $user->getId() && !$user->isAdmin()) {
            throw new HttpException(403, 'This action is unauthorized.');
        }

        return $loan;
    }

    public function addRepayment(User $user, int $loanId, int $scheduledRepaymentId, Request $request): void
    {
        /** @var Loan $loan */
        $loan = $this->loanRepository->find($loanId);
        if (!$loan) {
            throw new HttpException(404, 'Loan not found.');
        }

        $scheduledRepayment = $loan->findScheduledRepaymentById($scheduledRepaymentId);
        if (!$scheduledRepayment) {
            throw new HttpException(404, 'Scheduled repayment not found.');
        }

        if ($scheduledRepayment->getStatus() !== ScheduledRepayment::STATUS_PENDING) {
            throw new HttpException(422, 'Repayment has already been added.');
        }

        if ($scheduledRepayment->getLoan()->getCustomer()->getId() !== $user->getId()) {
            throw new HttpException(403, 'This action is unauthorized.');
        }

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            throw new HttpException(422, $validator->errors()->first());
        }

        $scheduledRepayment->setStatus(ScheduledRepayment::STATUS_PAID);
        $scheduledRepayment->setPaidAt(now());
        $scheduledRepayment->setPaidAmount($request->amount);

        $this->loanRepository->save($loan);

        // Check if all scheduled repayments for this loan are PAID
        if ($loan->hasAllScheduledRepaymentsPaid()) {
            $this->loanRepository->update($loan->getId(), ['status' => Loan::STATUS_PAID]);
        }
    }
}
