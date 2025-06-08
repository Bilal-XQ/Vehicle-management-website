<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FuelLog;
use App\Models\Vehicle;
use App\Models\User;
use Carbon\Carbon;

class FuelLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = Vehicle::where('fuel_type', '!=', 'electric')->get();
        $users = User::all();

        foreach ($vehicles as $vehicle) {
            // Create 3-8 fuel logs per vehicle
            $logCount = rand(3, 8);
            
            for ($i = 0; $i < $logCount; $i++) {
                $fueledAt = Carbon::now()->subDays(rand(7, 180));
                $loggedBy = $users->random();
                
                // Calculate realistic fuel data based on vehicle type
                $fuelData = $this->getFuelDataByVehicle($vehicle);
                
                FuelLog::create([
                    'vehicle_id' => $vehicle->id,
                    'logged_by' => $loggedBy->id,
                    'fueled_at' => $fueledAt,
                    'mileage' => max(1000, $vehicle->mileage - rand(100, 5000)),
                    'liters' => $fuelData['liters'],
                    'cost_per_liter' => $fuelData['cost_per_liter'],
                    'total_cost' => $fuelData['liters'] * $fuelData['cost_per_liter'],
                    'fuel_station' => $this->getRandomFuelStation(),
                    'full_tank' => rand(0, 100) > 20, // 80% chance of full tank
                    'notes' => $this->getRandomNotes(),
                ]);
            }
        }
    }

    private function getFuelDataByVehicle(Vehicle $vehicle): array
    {
        // Different vehicles have different tank sizes and fuel efficiency
        $vehicleProfiles = [
            'small_car' => ['min_liters' => 25, 'max_liters' => 45],
            'medium_car' => ['min_liters' => 35, 'max_liters' => 55],
            'large_car' => ['min_liters' => 45, 'max_liters' => 70],
            'truck' => ['min_liters' => 60, 'max_liters' => 100],
            'hybrid' => ['min_liters' => 30, 'max_liters' => 50],
        ];

        // Determine vehicle profile based on model
        $profile = $this->getVehicleProfile($vehicle);
        $profileData = $vehicleProfiles[$profile];

        // Generate realistic fuel data
        $liters = rand($profileData['min_liters'], $profileData['max_liters']);
        
        // Fuel prices vary by type and date
        $costPerLiter = $this->getFuelPrice($vehicle->fuel_type);

        return [
            'liters' => $liters,
            'cost_per_liter' => $costPerLiter,
        ];
    }

    private function getVehicleProfile(Vehicle $vehicle): string
    {
        $model = strtolower($vehicle->model);
        
        if (in_array($model, ['f-150', 'silverado', 'ram', 'tundra'])) {
            return 'truck';
        }
        
        if (in_array($model, ['prius', 'camry hybrid', 'accord hybrid'])) {
            return 'hybrid';
        }
        
        if (in_array($model, ['civic', 'corolla', 'sentra', 'elantra'])) {
            return 'small_car';
        }
        
        if (in_array($model, ['320i', '3 series', 'a4', 'c-class'])) {
            return 'large_car';
        }
        
        return 'medium_car'; // Default
    }

    private function getFuelPrice(string $fuelType): float
    {
        $basePrices = [
            'gasoline' => 1.45,
            'diesel' => 1.55,
            'lpg' => 0.85,
            'hybrid' => 1.45, // Uses gasoline
        ];

        $basePrice = $basePrices[$fuelType] ?? 1.45;
        
        // Add some price variation (±15%)
        $variation = rand(-15, 15) / 100;
        
        return round($basePrice * (1 + $variation), 3);
    }

    private function getRandomFuelStation(): string
    {
        $stations = [
            'Shell',
            'BP',
            'Exxon',
            'Chevron',
            'Mobil',
            'Texaco',
            'Sunoco',
            '76 Station',
            'Arco',
            'Marathon',
            'Speedway',
            'Wawa',
            'QuikTrip',
            'Casey\'s',
            'Circle K',
        ];

        return $stations[array_rand($stations)];
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            'Regular fill-up',
            'Highway trip refuel',
            'Emergency fuel stop',
            'Good fuel efficiency this tank',
            'Long distance travel',
            'City driving mostly',
            'Premium fuel used',
            null, // Many fuel logs won't have notes
            null,
            null,
            null,
        ];

        return $notes[array_rand($notes)];
    }
}
