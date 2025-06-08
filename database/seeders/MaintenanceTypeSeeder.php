<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaintenanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maintenanceTypes = [
            [
                'name' => 'Oil Change',
                'description' => 'Regular engine oil and filter change',
                'recommended_interval_km' => 10000,
                'recommended_interval_months' => 6,
            ],
            [
                'name' => 'Tire Rotation',
                'description' => 'Rotate tires to ensure even wear',
                'recommended_interval_km' => 12000,
                'recommended_interval_months' => 6,
            ],
            [
                'name' => 'Brake Inspection',
                'description' => 'Check brake pads, rotors, and fluid',
                'recommended_interval_km' => 20000,
                'recommended_interval_months' => 12,
            ],
            [
                'name' => 'Air Filter Replacement',
                'description' => 'Replace engine air filter',
                'recommended_interval_km' => 20000,
                'recommended_interval_months' => 12,
            ],
            [
                'name' => 'Transmission Service',
                'description' => 'Transmission fluid change and inspection',
                'recommended_interval_km' => 60000,
                'recommended_interval_months' => 24,
            ],
            [
                'name' => 'Coolant Flush',
                'description' => 'Replace engine coolant',
                'recommended_interval_km' => 60000,
                'recommended_interval_months' => 24,
            ],
            [
                'name' => 'Spark Plug Replacement',
                'description' => 'Replace spark plugs',
                'recommended_interval_km' => 50000,
                'recommended_interval_months' => 36,
            ],
            [
                'name' => 'Battery Test',
                'description' => 'Test battery and charging system',
                'recommended_interval_km' => null,
                'recommended_interval_months' => 12,
            ],
            [
                'name' => 'Wheel Alignment',
                'description' => 'Adjust wheel alignment',
                'recommended_interval_km' => 20000,
                'recommended_interval_months' => null,
            ],
            [
                'name' => 'General Inspection',
                'description' => 'Comprehensive vehicle inspection',
                'recommended_interval_km' => 20000,
                'recommended_interval_months' => 12,
            ],
        ];

        foreach ($maintenanceTypes as $type) {
            DB::table('maintenance_types')->insert([
                'name' => $type['name'],
                'description' => $type['description'],
                'recommended_interval_km' => $type['recommended_interval_km'],
                'recommended_interval_months' => $type['recommended_interval_months'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
