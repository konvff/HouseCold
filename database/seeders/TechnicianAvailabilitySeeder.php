<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Technician;
use App\Models\TechnicianAvailability;
use Carbon\Carbon;

class TechnicianAvailabilitySeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing availabilities
        TechnicianAvailability::truncate();

        $technicians = Technician::all();

        if ($technicians->count() === 0) {
            $this->command->info('No technicians found. Please run AdminUserSeeder first.');
            return;
        }

        foreach ($technicians as $technician) {
            // Create weekly recurring availability (Monday to Friday, 9 AM to 5 PM)
            $this->createWeeklyAvailability($technician);

            // Create some weekend availability (Saturday, 10 AM to 3 PM)
            $this->createWeekendAvailability($technician);
        }

        $this->command->info('Technician availability time slots seeded successfully!');
    }

    private function createWeeklyAvailability($technician)
    {
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($weekdays as $day) {
            TechnicianAvailability::create([
                'technician_id' => $technician->id,
                'day_of_week' => $day,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'start_date' => Carbon::now()->startOfWeek()->addDays(Carbon::parse('monday')->dayOfWeek - 1),
                'end_date' => null, // Indefinite
                'is_recurring' => true,
                'is_active' => true,
            ]);
        }
    }

    private function createWeekendAvailability($technician)
    {
        TechnicianAvailability::create([
            'technician_id' => $technician->id,
            'day_of_week' => 'saturday',
            'start_time' => '10:00:00',
            'end_time' => '15:00:00',
            'start_date' => Carbon::now()->startOfWeek()->addDays(5), // Saturday
            'end_date' => null, // Indefinite
            'is_recurring' => true,
            'is_active' => true,
        ]);
    }
}
