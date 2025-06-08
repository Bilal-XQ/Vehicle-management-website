<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleMakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $makes = [
            ['name' => 'Toyota', 'country' => 'Japan'],
            ['name' => 'Honda', 'country' => 'Japan'],
            ['name' => 'Ford', 'country' => 'USA'],
            ['name' => 'Chevrolet', 'country' => 'USA'],
            ['name' => 'BMW', 'country' => 'Germany'],
            ['name' => 'Mercedes-Benz', 'country' => 'Germany'],
            ['name' => 'Volkswagen', 'country' => 'Germany'],
            ['name' => 'Audi', 'country' => 'Germany'],
            ['name' => 'Nissan', 'country' => 'Japan'],
            ['name' => 'Hyundai', 'country' => 'South Korea'],
            ['name' => 'Kia', 'country' => 'South Korea'],
            ['name' => 'Mazda', 'country' => 'Japan'],
            ['name' => 'Subaru', 'country' => 'Japan'],
            ['name' => 'Volvo', 'country' => 'Sweden'],
            ['name' => 'Peugeot', 'country' => 'France'],
            ['name' => 'Renault', 'country' => 'France'],
            ['name' => 'Fiat', 'country' => 'Italy'],
            ['name' => 'Jeep', 'country' => 'USA'],
            ['name' => 'Land Rover', 'country' => 'UK'],            ['name' => 'Lexus', 'country' => 'Japan'],
            ['name' => 'Tesla', 'country' => 'USA'],
        ];

        foreach ($makes as $make) {
            DB::table('vehicle_makes')->insert([
                'name' => $make['name'],
                'country' => $make['country'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
