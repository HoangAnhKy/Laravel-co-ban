<?php

namespace Database\Seeders;

use App\Models\User as ModelsUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    public function run()
    {
        $data = [
            'name' => 'Admin',
            'level' => 0,
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123'),
        ];
        ModelsUser::create($data);
        $data = [
            'name' => 'Supper Admin',
            'level' => 1,
            'email' => 'sadmin@gmail.com',
            'password' => Hash::make('123'),
        ];
        ModelsUser::create($data);
    }
}
