<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class UserAchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'achievement_key' => 'lessons_watched',
            'achievement_message' => Arr::random([5, 10, 25, 50]) . ' Lessons Watched Achievement'
        ];
    }
}
