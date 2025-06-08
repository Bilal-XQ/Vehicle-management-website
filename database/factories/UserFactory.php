<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'role' => fake()->randomElement(User::getRoles()),
            'phone' => fake()->phoneNumber(),
            'department' => fake()->randomElement([
                'Fleet Management',
                'Operations',
                'Maintenance',
                'Administration',
                'Field Services',
                'Transportation'
            ]),
            'is_active' => fake()->boolean(90), // 90% chance of being active
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create an admin user
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    /**
     * Create a manager user
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_MANAGER,
            'is_active' => true,
        ]);
    }

    /**
     * Create a viewer user
     */
    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => User::ROLE_VIEWER,
            'is_active' => true,
        ]);
    }

    /**
     * Create an inactive user
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
