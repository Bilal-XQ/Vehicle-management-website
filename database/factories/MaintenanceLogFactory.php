<?php

namespace Database\Factories;

use App\Models\MaintenanceLog;
use App\Models\Vehicle;
use App\Models\MaintenanceType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceLog>
 */
class MaintenanceLogFactory extends Factory
{
    protected $model = MaintenanceLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maintenanceDate = fake()->dateTimeBetween('-2 years', 'now');
        $nextServiceDate = fake()->optional()->dateTimeBetween($maintenanceDate, '+1 year');
        
        return [
            'vehicle_id' => Vehicle::factory(),
            'maintenance_type_id' => MaintenanceType::factory(),
            'performed_by' => User::factory(),
            'service_provider' => fake()->optional()->company(),
            'maintenance_date' => $maintenanceDate,
            'mileage_at_service' => fake()->numberBetween(10000, 200000),
            'cost' => fake()->randomFloat(2, 50, 2000),
            'parts_used' => fake()->optional()->randomElement([
                [
                    ['name' => 'Oil Filter', 'part_number' => 'OF-123', 'cost' => 15.99],
                    ['name' => 'Engine Oil', 'part_number' => 'EO-456', 'cost' => 35.00]
                ],
                [
                    ['name' => 'Brake Pads', 'part_number' => 'BP-789', 'cost' => 89.99]
                ],
                [
                    ['name' => 'Air Filter', 'part_number' => 'AF-321', 'cost' => 25.50],
                    ['name' => 'Cabin Filter', 'part_number' => 'CF-654', 'cost' => 18.75]
                ]
            ]),
            'labor_hours' => fake()->randomFloat(1, 0.5, 8.0),
            'description' => fake()->sentence(),
            'next_service_mileage' => fake()->optional()->numberBetween(15000, 250000),
            'next_service_date' => $nextServiceDate,
            'warranty_until' => fake()->optional()->dateTimeBetween($maintenanceDate, '+2 years'),
            'receipt_number' => fake()->optional()->regexify('[A-Z]{2}-[0-9]{6}'),
            'status' => fake()->randomElement(MaintenanceLog::getStatuses()),
            'notes' => fake()->optional()->paragraph(),
        ];
    }

    /**
     * Create a completed maintenance log
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceLog::STATUS_COMPLETED,
            'maintenance_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    /**
     * Create a scheduled maintenance log
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceLog::STATUS_SCHEDULED,
            'maintenance_date' => fake()->dateTimeBetween('now', '+3 months'),
            'cost' => null, // No cost for scheduled maintenance
        ]);
    }

    /**
     * Create an expensive maintenance log
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'cost' => fake()->randomFloat(2, 1000, 5000),
            'labor_hours' => fake()->randomFloat(1, 4.0, 16.0),
            'status' => MaintenanceLog::STATUS_COMPLETED,
        ]);
    }

    /**
     * Create a routine maintenance log
     */
    public function routine(): static
    {
        return $this->state(fn (array $attributes) => [
            'cost' => fake()->randomFloat(2, 30, 150),
            'labor_hours' => fake()->randomFloat(1, 0.5, 2.0),
            'status' => MaintenanceLog::STATUS_COMPLETED,
        ]);
    }

    /**
     * Create an overdue maintenance log
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MaintenanceLog::STATUS_SCHEDULED,
            'next_service_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
