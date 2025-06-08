<?php

namespace Database\Factories;

use App\Models\MaintenanceType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceType>
 */
class MaintenanceTypeFactory extends Factory
{
    protected $model = MaintenanceType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Oil Change',
                'Tire Rotation',
                'Brake Inspection',
                'Battery Check',
                'Air Filter Replacement',
                'Transmission Service',
                'Coolant Flush',
                'Spark Plug Replacement',
                'Wheel Alignment',
                'Suspension Check',
                'Engine Tune-up',
                'Exhaust System Check',
                'Fuel System Cleaning',
                'AC Service',
                'Power Steering Service'
            ]),
            'description' => $this->faker->sentence(10),
            'category' => $this->faker->randomElement(['preventive', 'corrective', 'predictive', 'emergency']),
            'frequency_km' => $this->faker->randomElement([5000, 10000, 15000, 20000, 30000, 50000, null]),
            'estimated_cost' => $this->faker->randomFloat(2, 50, 1500),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the maintenance type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the maintenance type is preventive.
     */
    public function preventive(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'preventive',
            'frequency_km' => $this->faker->randomElement([5000, 10000, 15000, 20000]),
        ]);
    }

    /**
     * Indicate that the maintenance type is corrective.
     */
    public function corrective(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'corrective',
            'frequency_km' => null,
            'estimated_cost' => $this->faker->randomFloat(2, 200, 2000),
        ]);
    }

    /**
     * Indicate that the maintenance type is emergency.
     */
    public function emergency(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => 'emergency',
            'frequency_km' => null,
            'estimated_cost' => $this->faker->randomFloat(2, 500, 3000),
        ]);
    }

    /**
     * Indicate that the maintenance type is expensive.
     */
    public function expensive(): static
    {
        return $this->state(fn (array $attributes) => [
            'estimated_cost' => $this->faker->randomFloat(2, 1000, 5000),
        ]);
    }

    /**
     * Indicate that the maintenance type has high frequency.
     */
    public function highFrequency(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency_km' => $this->faker->randomElement([3000, 5000, 7500]),
        ]);
    }
}
