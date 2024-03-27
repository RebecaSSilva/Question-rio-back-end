<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;
Use Illuminate\Support\Str;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition()
    {
        $description = fake()->paragraph();
        $description = Str::limit($description, 40);
    
        return [
            'field_slug' => fake()->unique()->slug(),
            'field_title' => fake()->sentence(),
            'field_description' => $description,
            'field_type' => 'text', 
            'is_last' => 0,
            'mandatory' => 0,
            'value_key' => null,
            'form_id' => 1, 
        ];
    }
}
