<?php

namespace Database\Factories;

use App\Enums\StatusStudent;
use App\Models\course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'gender' => $this->faker->boolean(),
            'birthday' => $this->faker->dateTimeBetween('-25 years', '-20 years'),
            'course_id' => course::query()->inRandomOrder()->value('id'),
            'status' => $this->faker->randomElement(StatusStudent::arrStatus()),
        ];
    }
}
