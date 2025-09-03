<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServiceType;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'HVAC Installation',
                'description' => 'Professional installation of heating, ventilation, and air conditioning systems',
                'hourly_rate' => 85.00,
                'estimated_duration_minutes' => 240,
                'is_active' => true
            ],
            [
                'name' => 'Plumbing Repair',
                'description' => 'Fix leaks, clogs, and other plumbing issues',
                'hourly_rate' => 75.00,
                'estimated_duration_minutes' => 120,
                'is_active' => true
            ],
            [
                'name' => 'Electrical Work',
                'description' => 'Electrical repairs, installations, and safety inspections',
                'hourly_rate' => 90.00,
                'estimated_duration_minutes' => 180,
                'is_active' => true
            ],
            [
                'name' => 'Appliance Repair',
                'description' => 'Repair and maintenance of home appliances',
                'hourly_rate' => 70.00,
                'estimated_duration_minutes' => 90,
                'is_active' => true
            ],
            [
                'name' => 'Home Security Installation',
                'description' => 'Installation of security cameras, alarms, and smart home systems',
                'hourly_rate' => 95.00,
                'estimated_duration_minutes' => 300,
                'is_active' => true
            ],
            [
                'name' => 'General Consultation',
                'description' => 'Professional consultation for home improvement projects',
                'hourly_rate' => 60.00,
                'estimated_duration_minutes' => 60,
                'is_active' => true
            ]
        ];

        foreach ($services as $service) {
            ServiceType::create($service);
        }
    }
}
