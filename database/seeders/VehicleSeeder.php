<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\VehicleMake;
use Carbon\Carbon;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'make' => 'Toyota',
                'model' => 'Camry',
                'year' => 2022,
                'license_plate' => 'ABC-1234',
                'vin' => '1HGBH41JXMN109186',
                'status' => 'active',
                'fuel_type' => 'gasoline',
                'color' => 'Silver',
                'mileage' => 15000,
                'purchase_date' => '2022-01-15',
                'purchase_price' => 28000.00,
            ],
            [
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2023,
                'license_plate' => 'XYZ-5678',
                'vin' => '2HGFC2F59NH123456',
                'status' => 'active',
                'fuel_type' => 'gasoline',
                'color' => 'Blue',
                'mileage' => 8500,
                'purchase_date' => '2023-03-10',
                'purchase_price' => 25000.00,
            ],
            [
                'make' => 'Ford',
                'model' => 'F-150',
                'year' => 2021,
                'license_plate' => 'DEF-9012',
                'vin' => '1FTFW1ET5MFC12345',
                'status' => 'maintenance',
                'fuel_type' => 'gasoline',
                'color' => 'White',
                'mileage' => 35000,
                'purchase_date' => '2021-08-20',
                'purchase_price' => 35000.00,
            ],
            [
                'make' => 'BMW',
                'model' => '320i',
                'year' => 2022,
                'license_plate' => 'GHI-3456',
                'vin' => 'WBA5A5C54KA123456',
                'status' => 'active',
                'fuel_type' => 'gasoline',
                'color' => 'Black',
                'mileage' => 18000,
                'purchase_date' => '2022-05-12',
                'purchase_price' => 42000.00,
            ],
            [
                'make' => 'Chevrolet',
                'model' => 'Silverado',
                'year' => 2020,
                'license_plate' => 'JKL-7890',
                'vin' => '1GCRYDED5LZ123456',
                'status' => 'retired',
                'fuel_type' => 'gasoline',
                'color' => 'Red',
                'mileage' => 85000,
                'purchase_date' => '2020-02-15',
                'purchase_price' => 38000.00,
            ],
            [
                'make' => 'Tesla',
                'model' => 'Model 3',
                'year' => 2023,
                'license_plate' => 'ELE-0001',
                'vin' => '5YJ3E1EA9NF123456',
                'status' => 'active',
                'fuel_type' => 'electric',
                'color' => 'White',
                'mileage' => 5000,
                'purchase_date' => '2023-06-01',
                'purchase_price' => 45000.00,
            ],
            [
                'make' => 'Honda',
                'model' => 'CR-V',
                'year' => 2022,
                'license_plate' => 'SUV-2468',
                'vin' => '7FARW2H84NE123456',
                'status' => 'active',
                'fuel_type' => 'gasoline',
                'color' => 'Gray',
                'mileage' => 22000,
                'purchase_date' => '2022-09-10',
                'purchase_price' => 32000.00,
            ],
            [
                'make' => 'Toyota',
                'model' => 'Prius',
                'year' => 2023,
                'license_plate' => 'HYB-1357',
                'vin' => 'JTDKARFU9N3123456',
                'status' => 'active',
                'fuel_type' => 'hybrid',
                'color' => 'Green',
                'mileage' => 12000,
                'purchase_date' => '2023-01-20',
                'purchase_price' => 30000.00,
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            $make = VehicleMake::where('name', $vehicleData['make'])->first();
            
            if ($make) {
                Vehicle::create([
                    'vehicle_make_id' => $make->id,
                    'model' => $vehicleData['model'],
                    'year' => $vehicleData['year'],
                    'license_plate' => $vehicleData['license_plate'],
                    'vin' => $vehicleData['vin'],
                    'status' => $vehicleData['status'],
                    'fuel_type' => $vehicleData['fuel_type'],
                    'color' => $vehicleData['color'],
                    'mileage' => $vehicleData['mileage'],
                    'purchase_date' => Carbon::parse($vehicleData['purchase_date']),
                    'purchase_price' => $vehicleData['purchase_price'],
                    'registration_expiry' => Carbon::parse($vehicleData['purchase_date'])->addYear(),
                    'insurance_expiry' => Carbon::parse($vehicleData['purchase_date'])->addYear(),
                ]);
            }
        }
    }
}
