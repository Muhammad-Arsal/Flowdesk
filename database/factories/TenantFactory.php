<?php

namespace Database\Factories;

use App\Enums\TenantStatus;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'subdomain' => fake()->unique()->slug(2),
            'status' => TenantStatus::Active,
        ];
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TenantStatus::Suspended,
        ]);
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => TenantStatus::Trial,
        ]);
    }
}
