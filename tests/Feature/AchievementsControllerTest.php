<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AchievementsControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testIndexWithUserNotFound(): void
    {
        $this->get('/users/100000000/achievements')
            ->assertNotFound();
    }

    public function testIndex(): void
    {
        $user = User::factory()->create();

        $this->get("/users/{$user->id}/achievements")
            ->assertOk()
            ->assertExactJson([
                'unlocked_achievements' => [],
                'next_available_achievements' => [
                    'Comments Written Achievements' => 'First Comment Written',
                    'Lessons Watched Achievement' => 'First Lesson Watched'
                ],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaing_to_unlock_next_badge' => 4
            ]);
    }
}
