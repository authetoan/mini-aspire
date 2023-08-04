<?php

namespace Database\Seeders;

use App\Domain\Entities\User\Role;
use App\Domain\Entities\User\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // Get admin and customer roles
        $adminRole = Role::where('name', 'admin')->first();
        $customerRole = Role::where('name', 'customer')->first();

        // Assign roles to users
        User::find(1)->roles()->attach([$adminRole->id, $customerRole->id]);
        User::find(2)->roles()->attach([$customerRole->id]);
        // Add more role assignments as needed
    }
}
