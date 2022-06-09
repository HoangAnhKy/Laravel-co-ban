<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = [
            'name' => 'Admin',
            'lever' => 2,
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123'),
        ];
        \App\Models\User::create($data);
    }
}
