<?php

namespace Database\Factories;

use App\Models\DeviceMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeviceMessage>
 */
class DeviceMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'device_name' => fake()->unique()->slug(2),
            'payload' => ['level' => fake()->randomFloat(2, 0, 100)],
            'received_at' => now(),
        ];
    }
}
