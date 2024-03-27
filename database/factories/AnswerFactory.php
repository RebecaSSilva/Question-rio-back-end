<?php

namespace Database\Factories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition()
    {
        return [
            'form_id' => $this->faker->numberBetween(1, 10),
            'field_slug' => $this->faker->slug,
            'field_title' => $this->faker->sentence,
            'field_type' => $this->faker->randomElement(['text', 'email', 'number', 'checkbox', 'radio']),
            'value' => $this->faker->text,
            'value_key' => null,
            'is_last' => $this->faker->boolean(),
            'public_user_id' => $this->faker->uuid
        ];
    }
}