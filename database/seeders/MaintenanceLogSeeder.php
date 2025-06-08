<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use App\Models\User;
use Carbon\Carbon;

class MaintenanceLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicles = Vehicle::all();
        $maintenanceTypes = MaintenanceType::all();
        $users = User::where('role', '!=', 'viewer')->get();

        foreach ($vehicles as $vehicle) {
            // Create 2-5 maintenance logs per vehicle
            $logCount = rand(2, 5);
            
            for ($i = 0; $i < $logCount; $i++) {
                $performedAt = Carbon::now()->subDays(rand(30, 365));
                $maintenanceType = $maintenanceTypes->random();
                $performedBy = $users->random();
                
                MaintenanceLog::create([
                    'vehicle_id' => $vehicle->id,
                    'maintenance_type_id' => $maintenanceType->id,
                    'performed_by' => $performedBy->id,
                    'title' => $maintenanceType->name . ' - ' . $vehicle->license_plate,
                    'description' => $this->getRandomDescription($maintenanceType->name),
                    'performed_at' => $performedAt,
                    'mileage_at_service' => max(1000, $vehicle->mileage - rand(1000, 10000)),
                    'cost' => $this->getRandomCost($maintenanceType->name),
                    'service_provider' => $this->getRandomServiceProvider(),
                    'priority' => $this->getRandomPriority(),
                    'status' => 'completed',
                    'next_due_date' => $this->calculateNextDueDate($performedAt, $maintenanceType),
                    'next_due_mileage' => $this->calculateNextDueMileage($vehicle->mileage, $maintenanceType),
                    'parts_used' => $this->getRandomPartsUsed($maintenanceType->name),
                    'notes' => $this->getRandomNotes(),
                ]);
            }
        }
    }

    private function getRandomDescription(string $maintenanceType): string
    {
        $descriptions = [
            'Oil Change' => [
                'Replaced engine oil with 5W-30 synthetic oil and new oil filter',
                'Performed regular oil change service with full synthetic oil',
                'Changed engine oil and filter, checked fluid levels',
            ],
            'Tire Rotation' => [
                'Rotated all four tires to ensure even wear pattern',
                'Performed tire rotation and checked tire pressure',
                'Rotated tires and inspected for unusual wear patterns',
            ],
            'Brake Inspection' => [
                'Inspected brake pads and rotors, all within acceptable limits',
                'Checked brake system, replaced worn brake pads',
                'Brake inspection completed, brake fluid topped off',
            ],
            'Air Filter Replacement' => [
                'Replaced dirty air filter with new OEM filter',
                'Changed engine air filter, improving airflow',
                'Installed new air filter, old one was heavily contaminated',
            ],
        ];

        $typeDescriptions = $descriptions[$maintenanceType] ?? ['Standard maintenance performed'];
        return $typeDescriptions[array_rand($typeDescriptions)];
    }

    private function getRandomCost(string $maintenanceType): float
    {
        $costs = [
            'Oil Change' => [45, 85],
            'Tire Rotation' => [25, 50],
            'Brake Inspection' => [150, 400],
            'Air Filter Replacement' => [20, 45],
            'Transmission Service' => [200, 350],
            'Coolant Flush' => [100, 180],
            'Spark Plug Replacement' => [80, 150],
            'Battery Test' => [0, 25],
            'Wheel Alignment' => [75, 120],
            'General Inspection' => [50, 100],
        ];

        $range = $costs[$maintenanceType] ?? [50, 150];
        return rand($range[0], $range[1]);
    }

    private function getRandomServiceProvider(): string
    {
        $providers = [
            'AutoZone Service Center',
            'Jiffy Lube',
            'Valvoline Instant Oil Change',
            'Firestone Complete Auto Care',
            'Midas Auto Service',
            'Pep Boys',
            'NTB Tire & Service Centers',
            'Goodyear Auto Service',
            'Local Garage Services',
            'Dealership Service Center',
        ];

        return $providers[array_rand($providers)];
    }

    private function getRandomPriority(): string
    {
        $priorities = ['low', 'medium', 'high'];
        $weights = [60, 30, 10]; // 60% low, 30% medium, 10% high
        
        $rand = rand(1, 100);
        if ($rand <= 60) return 'low';
        if ($rand <= 90) return 'medium';
        return 'high';
    }

    private function calculateNextDueDate(Carbon $performedAt, MaintenanceType $type): ?Carbon
    {
        if ($type->recommended_interval_months) {
            return $performedAt->copy()->addMonths($type->recommended_interval_months);
        }
        return null;
    }

    private function calculateNextDueMileage(int $currentMileage, MaintenanceType $type): ?int
    {
        if ($type->recommended_interval_km) {
            return $currentMileage + $type->recommended_interval_km;
        }
        return null;
    }

    private function getRandomPartsUsed(string $maintenanceType): ?array
    {
        $parts = [
            'Oil Change' => [
                ['name' => 'Engine Oil', 'quantity' => '5L', 'part_number' => 'OIL-5W30-5L'],
                ['name' => 'Oil Filter', 'quantity' => '1', 'part_number' => 'OF-12345'],
            ],
            'Air Filter Replacement' => [
                ['name' => 'Air Filter', 'quantity' => '1', 'part_number' => 'AF-67890'],
            ],
            'Brake Inspection' => [
                ['name' => 'Brake Pads', 'quantity' => '4', 'part_number' => 'BP-FRONT-SET'],
                ['name' => 'Brake Fluid', 'quantity' => '1L', 'part_number' => 'BF-DOT4-1L'],
            ],
        ];

        return $parts[$maintenanceType] ?? null;
    }

    private function getRandomNotes(): ?string
    {
        $notes = [
            'Service completed without issues',
            'Vehicle in excellent condition',
            'Minor wear noted, will monitor',
            'Recommended next service in 6 months',
            'Customer notified of upcoming maintenance needs',
            null, // Some logs won't have notes
            null,
        ];

        return $notes[array_rand($notes)];
    }
}
