<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed permissions
        $this->call([
            PermissionSeeder::class,
        ]);
        
        // Create super admin user
        $this->call([
            SuperAdminSeeder::class,
        ]);
        
        $this->command->info('Database seeded successfully!');
    }
}
