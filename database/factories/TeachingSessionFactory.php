<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\TeachingSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeachingSessionFactory extends Factory
{
    protected $model = TeachingSession::class;

    public function definition(): array
    {
        static $sequence = 1;

        $users = User::all();

    	return [
    	    'session_name' => 'Session ' . ($sequence++),
            'user_id' => $users->count() > 0 ? $users->random()->id : User::factory(),
            'start_time' => $this->faker->dateTimeBetween('-60 days', '+30 days'),
            'end_time' => $this->faker->dateTimeBetween('-30 days', '+60 days')
    	];
    }
}
