<?php

namespace Database\Factories;

use App\Models\Context;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContextFactory extends Factory
{
    protected $model = Context::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
        ];
    }
}
