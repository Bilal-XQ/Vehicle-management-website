<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\VehicleMake;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->numberBetween(2010, 2024);
        $purchaseDate = fake()->dateTimeBetween($year . '-01-01', $year . '-12-31');
        
        return [
            'make_id' => VehicleMake::factory(),
            'model' => fake()->randomElement([
                'Sedan', 'SUV', 'Truck', 'Van', 'Coupe', 'Hatchback',
                'Camry', 'Accord', 'F-150', 'Silverado', 'Explorer',
                'Pilot', 'Transit', 'Sprinter', 'Civic', 'Corolla'
            ]),
            'year' => $year,
            'license_plate' => fake()->regexify('[A-Z]{3}-[0-9]{4}'),
            'vin' => fake()->regexify('[A-HJ-NPR-Z0-9]{17}'),
            'color' => fake()->colorName(),
            'fuel_type' => fake()->randomElement(Vehicle::getFuelTypes()),
            'engine_size' => fake()->randomFloat(1, 1.0, 6.0),
            'transmission' => fake()->randomElement(Vehicle::getTransmissionTypes()),
            'mileage' => fake()->numberBetween(0, 200000),
            'purchase_date' => $purchaseDate,
            'purchase_price' => fake()->randomFloat(2, 15000, 75000),
            'status' => fake()->randomElement(Vehicle::getStatuses()),
            'insurance_expiry' => fake()->dateTimeBetween('now', '+2 years'),
            'registration_expiry' => fake()->dateTimeBetween('now', '+1 year'),
            'inspection_expiry' => fake()->dateTimeBetween('now', '+6 months'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Create an active vehicle
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Vehicle::STATUS_ACTIVE,
        ]);
    }

    /**
     * Create a vehicle in maintenance
     */
    public function inMaintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Vehicle::STATUS_MAINTENANCE,
        ]);
    }

    /**
     * Create a high mileage vehicle
     */
    public function highMileage(): static
    {
        return $this->state(fn (array $attributes) => [
            'mileage' => fake()->numberBetween(150000, 300000),
            'year' => fake()->numberBetween(2005, 2015),
        ]);
    }

    /**
     * Create a new vehicle
     */
    public function new(): static
    {
        return $this->state(fn (array $attributes) => [
            'mileage' => fake()->numberBetween(0, 15000),
            'year' => fake()->numberBetween(2022, 2024),
            'status' => Vehicle::STATUS_ACTIVE,
        ]);
    }

    /**
     * Create an electric vehicle
     */
    public function electric(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => Vehicle::FUEL_ELECTRIC,
            'engine_size' => null, // Electric vehicles don't have traditional engines
        ]);
    }
}
