<?php

namespace Database\Factories\Domain\Entities\Loan;

use App\Domain\Entities\Loan\Loan;
use App\Domain\Entities\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Entities\Loan\Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(1000, 100000),
            'term' => $this->faker->numberBetween(1, 12),
            'request_date' => Carbon::now()->subDays($this->faker->numberBetween(1, 30)),
            'status' => Loan::STATUS_PENDING,
            'customer_id' => function () {
                return User::factory()->create()->id;
            },

        ];
    }
}
