<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bouquet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'role' => 'user',
            'password' => 'password',
        ]);

        // Seller user for login (password: password)
        User::firstOrCreate([
            'email' => 'seller@example.com',
        ], [
            'name' => 'Seller User',
            'password' => 'password',
            'role' => 'seller',
        ]);

        // Sample bouquets
        \App\Models\Bouquet::firstOrCreate([
            'name' => 'Classic Rose',
        ], [
            'description' => 'Kombinasi mawar merah dan putih, elegan.',
            'price' => 150000,
            'image' => null,
            'available' => true,
        ]);
        \App\Models\Bouquet::firstOrCreate([
            'name' => 'Sunny Mix',
        ], [
            'description' => 'Buket cerah dengan matahari dan bunga campuran.',
            'price' => 120000,
            'image' => null,
            'available' => true,
        ]);
    }
}
