<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Technician;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@housecall.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        // Create technician user
        $technician = User::create([
            'name' => 'John Technician',
            'email' => 'tech@housecall.com',
            'password' => bcrypt('password123'),
            'role' => 'technician'
        ]);

        // Create technician profile
        Technician::create([
            'user_id' => $technician->id,
            'phone' => '+1234567890',
            'specialties' => ['HVAC', 'Plumbing'],
            'status' => 'active',
            'hourly_rate' => 75.00
        ]);

        // Create sample customer user
        $customer = User::create([
            'name' => 'Jane Customer',
            'email' => 'customer@housecall.com',
            'password' => bcrypt('password123'),
            'role' => 'customer'
        ]);

        $this->command->info('Sample users created successfully!');
        $this->command->info('Admin: admin@housecall.com / password123');
        $this->command->info('Technician: tech@housecall.com / password123');
        $this->command->info('Customer: customer@housecall.com / password123');
    }
}
