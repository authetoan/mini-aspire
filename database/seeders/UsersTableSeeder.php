<?php

namespace Database\Seeders;

// database/seeders/UsersTableSeeder.php

use App\Domain\Entities\User\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create customer user
        User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ]);

        // Add more users as needed
    }
}
