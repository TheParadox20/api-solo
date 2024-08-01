<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Create a bot returning user instance
     */
    public static function bot(): User
    {
        $faker = \Faker\Factory::create();
        $user = [
            'name' => $faker->name,
            'phone' => $faker->phoneNumber,
            'password' => ' *',
        ];
        return User::create($user);
    }
    /**
     * Run the database seeds.
     */
    public static function run(): void{}
}
