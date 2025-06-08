<?php

namespace Database\Factories;

use App\Models\FuelLog;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FuelLog>
 */
class FuelLogFactory extends Factory
{
    protected $model = FuelLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->randomFloat(3, 5, 25); // 5-25 gallons
        $pricePerUnit = fake()->randomFloat(3, 2.50, 5.00); // $2.50-$5.00 per gallon
        $totalCost = round($quantity * $pricePerUnit, 2);
        $milesDriven = fake()->numberBetween(200, 500);
        
        return [
            'vehicle_id' => Vehicle::factory(),
            'logged_by' => User::factory(),
            'fuel_date' => fake()->dateTimeBetween('-6 months', 'now'),
            'odometer_reading' => fake()->numberBetween(10000, 200000),
            'quantity' => $quantity,
            'price_per_unit' => $pricePerUnit,
            'total_cost' => $totalCost,
            'fuel_type' => fake()->randomElement(FuelLog::getFuelTypes()),
            'gas_station' => fake()->randomElement([
                'Shell', 'BP', 'Exxon', 'Chevron', 'Mobil', 'Texaco',
                'Sunoco', 'Citgo', 'Marathon', 'Speedway', 'Wawa',
                'QuikTrip', 'Circle K', 'Casey\'s', '7-Eleven'
            ]),
            'miles_driven' => $milesDriven,
            'is_full_tank' => fake()->boolean(80), // 80% chance of full tank
            'notes' => fake()->optional(30)->sentence(), // 30% chance of having notes
        ];
    }

    /**
     * Create a full tank fuel log
     */
    public function fullTank(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_full_tank' => true,
            'quantity' => fake()->randomFloat(3, 12, 25), // Larger quantity for full tank
        ]);
    }

    /**
     * Create a partial fill fuel log
     */
    public function partialFill(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_full_tank' => false,
            'quantity' => fake()->randomFloat(3, 3, 12), // Smaller quantity for partial fill
        ]);
    }

    /**
     * Create an expensive fuel log
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'price_per_unit' => fake()->randomFloat(3, 4.50, 6.00),
            'quantity' => fake()->randomFloat(3, 15, 25),
            'total_cost' => function (array $attributes) {
                return round($attributes['quantity'] * $attributes['price_per_unit'], 2);
            },
        ]);
    }

    /**
     * Create a fuel log with good efficiency
     */
    public function efficient(): static
    {
        return $this->state(fn (array $attributes) => [
            'miles_driven' => fake()->numberBetween(400, 600), // High miles
            'quantity' => fake()->randomFloat(3, 8, 15), // Lower fuel consumption
            'is_full_tank' => true,
        ]);
    }

    /**
     * Create a fuel log with poor efficiency
     */
    public function inefficient(): static
    {
        return $this->state(fn (array $attributes) => [
            'miles_driven' => fake()->numberBetween(150, 300), // Low miles
            'quantity' => fake()->randomFloat(3, 15, 25), // Higher fuel consumption
            'is_full_tank' => true,
        ]);
    }

    /**
     * Create a recent fuel log
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_date' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create a diesel fuel log
     */
    public function diesel(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => FuelLog::FUEL_DIESEL,
            'price_per_unit' => fake()->randomFloat(3, 3.00, 4.50), // Diesel typically costs more
        ]);
    }

    /**
     * Create an electric vehicle charging log
     */
    public function electric(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => FuelLog::FUEL_ELECTRIC,
            'quantity' => fake()->randomFloat(3, 20, 80), // kWh instead of gallons
            'price_per_unit' => fake()->randomFloat(3, 0.10, 0.30), // Price per kWh
            'gas_station' => fake()->randomElement([
                'Tesla Supercharger', 'ChargePoint', 'EVgo', 'Electrify America',
                'Blink', 'SemaConnect', 'Volta', 'Greenlots'
            ]),
            'total_cost' => function (array $attributes) {
                return round($attributes['quantity'] * $attributes['price_per_unit'], 2);
            },
        ]);
    }
}
