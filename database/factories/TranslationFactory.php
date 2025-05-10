<?php

namespace Database\Factories;

use App\Models\Translation;
use App\Models\Locale;
use App\Models\Context;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TranslationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $locale = Locale::inRandomOrder()->first();
        $context = Context::inRandomOrder()->first();

        return [
            'key' => Str::random(70),
            'content' => $this->faker->sentence,
            'locale_id' => $locale->id,
            'context_id' => $context->id,
        ];
    }
}
