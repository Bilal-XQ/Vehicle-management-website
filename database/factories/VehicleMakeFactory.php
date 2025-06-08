<?php

namespace Database\Factories;

use App\Models\VehicleMake;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleMake>
 */
class VehicleMakeFactory extends Factory
{
    protected $model = VehicleMake::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $makes = [
            ['name' => 'Toyota', 'country' => 'Japan'],
            ['name' => 'Honda', 'country' => 'Japan'],
            ['name' => 'Ford', 'country' => 'USA'],
            ['name' => 'Chevrolet', 'country' => 'USA'],
            ['name' => 'Nissan', 'country' => 'Japan'],
            ['name' => 'BMW', 'country' => 'Germany'],
            ['name' => 'Mercedes-Benz', 'country' => 'Germany'],
            ['name' => 'Audi', 'country' => 'Germany'],
            ['name' => 'Volkswagen', 'country' => 'Germany'],
            ['name' => 'Hyundai', 'country' => 'South Korea'],
            ['name' => 'Kia', 'country' => 'South Korea'],
            ['name' => 'Mazda', 'country' => 'Japan'],
            ['name' => 'Subaru', 'country' => 'Japan'],
            ['name' => 'Volvo', 'country' => 'Sweden'],
            ['name' => 'Jeep', 'country' => 'USA'],
        ];

        $make = fake()->randomElement($makes);

        return [
            'name' => $make['name'],
            'country' => $make['country'],
            'is_active' => fake()->boolean(95), // 95% chance of being active
        ];
    }

    /**
     * Create an active make
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive make
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a Japanese make
     */
    public function japanese(): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => 'Japan',
        ]);
    }

    /**
     * Create an American make
     */
    public function american(): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => 'USA',
        ]);
    }

    /**
     * Create a German make
     */
    public function german(): static
    {
        return $this->state(fn (array $attributes) => [
            'country' => 'Germany',
        ]);
    }
}
