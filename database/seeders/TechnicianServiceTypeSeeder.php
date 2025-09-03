<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Technician;
use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;

class TechnicianServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing relationships
        DB::table('technician_service_type')->truncate();

        // Get all technicians and service types
        $technicians = Technician::all();
        $serviceTypes = ServiceType::all();

        // Assign service types to technicians
        foreach ($technicians as $technician) {
            // Randomly assign 2-4 service types to each technician
            $randomServiceTypes = $serviceTypes->random(rand(2, 4));

            foreach ($randomServiceTypes as $serviceType) {
                DB::table('technician_service_type')->insert([
                    'technician_id' => $technician->id,
                    'service_type_id' => $serviceType->id,
                    'is_primary' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Technician-Service Type relationships seeded successfully!');
    }
}
