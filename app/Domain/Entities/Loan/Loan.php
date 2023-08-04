<?php
declare(strict_types=1);

namespace App\Domain\Entities\Loan;

use App\Domain\Entities\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Collection\Collection;

class Loan extends Model
{
    use HasFactory;
    use SoftDeletes;

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_PAID = 'PAID';

    protected $fillable = ['amount', 'term', 'request_date', 'status', 'customer_id'];

    protected $casts = [
        'request_date' => 'date',
        'created_at' => 'date',
        'updated_at' => 'date',
        'deleted_at' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function scheduledRepayments()
    {
        return $this->hasMany(ScheduledRepayment::class, 'loan_id');
    }

    public function getScheduledRepayments()
    {
        return $this->scheduledRepayments;
    }

    public function addScheduledRepayment(ScheduledRepayment $scheduledRepayment)
    {
        if (!isset($this->scheduledRepayments)) {
            $this->scheduledRepayments = new Collection(ScheduledRepayment::class);
        }

        $this->scheduledRepayments->add($scheduledRepayment);
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function getRequestDate()
    {
        return $this->request_date;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getCustomer(): User
    {
        return $this->customer;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setCustomer(User $customer)
    {
        $this->customer = $customer;
    }

    public function setAmount(float $amount)
    {
        $this->amount = $amount;
    }

    public function setTerm(int $term)
    {
        $this->term = $term;
    }

    public function setRequestDate(\DateTime $requestDate)
    {
        $this->request_date = $requestDate;
    }

    public function setScheduledRepayments(Collection $scheduledRepayments)
    {
        $this->scheduledRepayments = $scheduledRepayments;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    public function hasAllScheduledRepaymentsPaid(): bool
    {
        $scheduledRepayments = $this->getScheduledRepayments();

        if (!isset($scheduledRepayments)) {
            return false;
        }

        foreach ($scheduledRepayments as $scheduledRepayment) {
            if ($scheduledRepayment->getStatus() !== ScheduledRepayment::STATUS_PAID) {
                return false;
            }
        }

        return true;
    }

    public function findScheduledRepaymentById(int $id): ?ScheduledRepayment
    {
        $scheduledRepayments = $this->getScheduledRepayments();

        if (!$scheduledRepayments) {
            return null;
        }

        foreach ($scheduledRepayments as $scheduledRepayment) {
            if ($scheduledRepayment->getId() === $id) {
                return $scheduledRepayment;
            }
        }

        return null;
    }
}
