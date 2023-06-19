<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $adminUser=User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 12341234,
        ]);
        $adminUser->assignRole(['admin']);
        $adminUser->save();
    }
}
