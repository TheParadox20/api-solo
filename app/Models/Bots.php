<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bots extends Model
{
    use HasFactory;
    protected $fillable = [ 'user_id'];

    public static function create(): User
    {
        $faker = fake();
        $user = [
            'name' => $faker->name,
            'phone' => $faker->phoneNumber,
            'password' => ' *',
        ];
        $botUser = User::create($user);
        $botUser->phone_verified_at = $faker->dateTime();
        // $botUser->save();
        $bot = new Bots();
        $bot->user_id = $botUser->id;
        $bot->save();
        return $botUser;
    }
}
