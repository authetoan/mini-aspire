<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Services\AuthService;
use App\Domain\Services\LoanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function __construct(
        private readonly LoanService $loanService,
        private readonly AuthService $authService,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $loanEntity = $this->loanService->storeLoan($request);
        $result = [
            'id' => $loanEntity->getId(),
            'customer_id' => $loanEntity->getCustomer()->getId(),
            'amount' => $loanEntity->getAmount(),
            'term' => $loanEntity->getTerm(),
            'request_date' => $loanEntity->getRequestDate()->format('Y-m-d'),
            'status' => $loanEntity->getStatus(),
            'scheduled_repayments' => [],
        ];
        foreach ($loanEntity->getScheduledRepayments() as $scheduledRepaymentEntity) {
            $result['scheduled_repayments'][] = [
                'id' => $scheduledRepaymentEntity->getId(),
                'due_date' => $scheduledRepaymentEntity->getDueDate()->format('Y-m-d'),
                'amount' => $scheduledRepaymentEntity->getAmount(),
                'status' => $scheduledRepaymentEntity->getStatus(),
            ];
        }

        return response()->json($result, 201);
    }

    public function approve(int $loanId): JsonResponse
    {
        $authUser = $this->authService->getAuthenticatedUser();

        $this->loanService->approve($authUser, $loanId);

        return response()->json(['message' => 'Loan approved successfully.']);
    }

    public function viewCustomerLoans(): JsonResponse
    {
        $result = [];
        $authUser = $this->authService->getAuthenticatedUser();
        $loans = $this->loanService->getCustomerLoans($authUser);

        if ($loans) {
            foreach ($loans as $loan) {
                $result[] = [
                    'id' => $loan->getId(),
                    'customer_id' => $loan->customer->getId(),
                    'amount' => $loan->getAmount(),
                    'term' => $loan->getTerm(),
                    'request_date' => $loan->getRequestDate()->format('Y-m-d'),
                    'status' => $loan->getStatus(),
                    'scheduled_repayments' => [],
                ];
            }
        }

        return response()->json($result);
    }

    public function show(int $loanId): JsonResponse
    {
        $authUser = $this->authService->getAuthenticatedUser();
        $loan = $this->loanService->getCustomerLoan($authUser, $loanId);
        $result = [
            'id' => $loan->getId(),
            'customer_id' => $loan->customer->getId(),
            'amount' => $loan->getAmount(),
            'term' => $loan->getTerm(),
            'request_date' => $loan->getRequestDate()->format('Y-m-d'),
            'status' => $loan->getStatus(),
            'scheduled_repayments' => [],
        ];
        foreach ($loan->getScheduledRepayments() as $scheduledRepaymentEntity) {
            $result['scheduled_repayments'][] = [
                'id' => $scheduledRepaymentEntity->getId(),
                'due_date' => $scheduledRepaymentEntity->getDueDate()->format('Y-m-d'),
                'amount' => $scheduledRepaymentEntity->getAmount(),
                'status' => $scheduledRepaymentEntity->getStatus(),
            ];
        }

        return response()->json($result);
    }

    public function addRepayment(int $loanId, int $scheduledRepaymentId, Request $request): JsonResponse
    {
        $authUser = $this->authService->getAuthenticatedUser();
        $this->loanService->addRepayment($authUser, $loanId, $scheduledRepaymentId, $request);
        return response()->json(['message' => 'Repayment added successfully.']);
    }
}
