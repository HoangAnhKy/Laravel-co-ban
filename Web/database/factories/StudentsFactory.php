<?php

namespace Database\Factories;

use App\Enums\StudentStatus;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Students>
 */
class StudentsFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'gender' => $this->faker->boolean(),
            'birthdate' => $this->faker->dateTimeBetween('-30 years', '-18 years'),
            'status' => $this->faker->randomElement(StudentStatus::asArray()),
            'course_id' => Course::query()->inRandomOrder()->value('id'),
        ];
    }
}
