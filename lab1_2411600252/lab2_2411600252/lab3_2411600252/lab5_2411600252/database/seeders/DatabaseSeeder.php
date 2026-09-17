<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Development/test user for logging into the system (see README for credentials).
        User::firstOrCreate(
            ['email' => 'admin@ecommerce.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            ProductSeeder::class,
            InventoryTransactionSeeder::class,
        ]);
    }
}
