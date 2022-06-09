<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Students;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Course::factory(10)->create();
        Students::factory(500)->create();
        $this->call([
            User::class,
        ]);
    }
}
