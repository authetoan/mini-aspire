<?php

namespace Database\Factories\Domain\Entities\User;

use App\Domain\Entities\User\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Entities\User\Role>
 */
class RoleFactory extends Factory
{

    protected $model = Role::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['admin', 'customer']),
        ];
    }
}
