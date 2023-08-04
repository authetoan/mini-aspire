<?php

namespace Database\Seeders;

// database/seeders/RolesTableSeeder.php

use App\Domain\Entities\User\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'customer']);
        // Add more roles as needed
    }
}
