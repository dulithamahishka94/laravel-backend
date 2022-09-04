<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'type'=> User::ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Default User',
            'email' => 'user@user.com',
            'password' => bcrypt('password'),
            'type'=> User::DEFAULT_USER,
        ]);
    }
}
