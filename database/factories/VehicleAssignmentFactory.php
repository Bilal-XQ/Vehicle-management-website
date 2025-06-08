<?php

namespace Database\Factories;

use App\Models\VehicleAssignment;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleAssignment>
 */
class VehicleAssignmentFactory extends Factory
{
    protected $model = VehicleAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assignedAt = $this->faker->dateTimeBetween('-2 years', 'now');
        $isActive = $this->faker->boolean(70); // 70% chance of being active

        return [
            'vehicle_id' => Vehicle::factory(),
            'user_id' => User::factory(),
            'assigned_at' => $assignedAt,
            'unassigned_at' => $isActive ? null : $this->faker->dateTimeBetween($assignedAt, 'now'),
            'notes' => $this->faker->optional(0.6)->sentence(8),
            'unassignment_reason' => $isActive ? null : $this->faker->optional(0.8)->randomElement([
                'Employee left company',
                'Vehicle reassignment',
                'End of project',
                'Vehicle maintenance',
                'Policy change',
                'Driver request'
            ]),
        ];
    }

    /**
     * Indicate that the assignment is currently active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'unassigned_at' => null,
            'unassignment_reason' => null,
        ]);
    }

    /**
     * Indicate that the assignment is inactive.
     */
    public function inactive(): static
    {
        return $this->state(function (array $attributes) {
            $assignedAt = Carbon::parse($attributes['assigned_at']);
            return [
                'unassigned_at' => $this->faker->dateTimeBetween($assignedAt, 'now'),
                'unassignment_reason' => $this->faker->randomElement([
                    'Employee left company',
                    'Vehicle reassignment',
                    'End of project',
                    'Vehicle maintenance',
                    'Policy change',
                    'Driver request'
                ]),
            ];
        });
    }

    /**
     * Indicate that the assignment is long-term (over 6 months).
     */
    public function longTerm(): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_at' => $this->faker->dateTimeBetween('-2 years', '-6 months'),
            'unassigned_at' => null,
            'unassignment_reason' => null,
        ]);
    }

    /**
     * Indicate that the assignment is short-term (less than 3 months).
     */
    public function shortTerm(): static
    {
        return $this->state(function (array $attributes) {
            $assignedAt = $this->faker->dateTimeBetween('-3 months', 'now');
            $unassignedAt = $this->faker->dateTimeBetween($assignedAt, 'now');
            
            return [
                'assigned_at' => $assignedAt,
                'unassigned_at' => $unassignedAt,
                'unassignment_reason' => $this->faker->randomElement([
                    'Temporary assignment completed',
                    'Short-term project ended',
                    'Driver rotation'
                ]),
            ];
        });
    }

    /**
     * Indicate that the assignment was recently created.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'unassigned_at' => null,
            'unassignment_reason' => null,
        ]);
    }

    /**
     * Indicate that the assignment has detailed notes.
     */
    public function withNotes(): static
    {
        return $this->state(fn (array $attributes) => [
            'notes' => $this->faker->paragraph(3),
        ]);
    }
}
