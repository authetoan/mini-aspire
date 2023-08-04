<?php
declare(strict_types=1);

namespace App\Domain\Entities\Loan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DateTimeInterface;

class ScheduledRepayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_PENDING = 'PENDING';
    const STATUS_PAID = 'PAID';

    protected $fillable = ['due_date', 'amount', 'status', 'loan_id', 'paid_amount', 'paid_at'];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'date',
        'created_at' => 'date',
        'updated_at' => 'date',
        'deleted_at' => 'date',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getDueDate(): DateTimeInterface
    {
        return $this->due_date;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPaidAmount(): float
    {
        return $this->paid_amount;
    }

    public function getPaidAt(): ?DateTimeInterface
    {
        return $this->paid_at;
    }

    public function setPaidAmount(float $paidAmount): void
    {
        $this->paid_amount = $paidAmount;
    }

    public function setPaidAt(DateTimeInterface $paidAt): void
    {
        $this->paid_at = $paidAt;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function isPaid(): bool
    {
        return $this->status == self::STATUS_PAID;
    }

    public function setLoan(Loan $loan): void
    {
        $this->loan_id = $loan->getId();
        $this->loan = $loan;
    }

    public function getLoan(): Loan
    {
        return $this->loan;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function setDueDate(DateTimeInterface $dueDate): void
    {
        $this->due_date = $dueDate;
    }
}
