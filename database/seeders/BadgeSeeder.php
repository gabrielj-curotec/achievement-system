<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Badge::firstOrCreate([
            'name' => 'Beginner',
            'required_achievements' => 0
        ]);
        Badge::firstOrCreate([
            'name' => 'Intermediate',
            'required_achievements' => 4
        ]);
        Badge::firstOrCreate([
            'name' => 'Advanced',
            'required_achievements' => 8
        ]);
        Badge::firstOrCreate([
            'name' => 'Master',
            'required_achievements' => 10
        ]);
    }
}
