<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Models\Comment;
use App\Models\User;
use App\Services\Achievements\CommentsWrittenAchievementsService;
use Tests\TestCase;

class CommentsWrittenAchievementsServiceTest extends TestCase
{
    private CommentsWrittenAchievementsService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new CommentsWrittenAchievementsService();
    }

    private function generateUserComments(int $amount): User
    {
        $user = User::factory()->create();
        Comment::factory()->count($amount)->create(['user_id' => $user->id]);

        return $user;
    }

    public function testUnlockFirstCommentWrittenAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        $user = $this->generateUserComments(1);

        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => 'First Comment Written'
        ]);
    }

    public function testUnlock3LessonsWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        $user = $this->generateUserComments(3);

        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => '3 Comments Written'
        ]);
    }

    public function testUnlock5LessonsWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        $user = $this->generateUserComments(5);

        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => '5 Comments Written'
        ]);
    }

    public function testUnlock10LessonsWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        $user = $this->generateUserComments(10);

        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => '10 Comments Written'
        ]);
    }

    public function testUnlock20LessonsWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        $user = $this->generateUserComments(20);

        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => '20 Comments Written'
        ]);
    }

    public function testGetNextAvailableAchievements(): void
    {
        $achievements = [
            1 => 'First Comment Written',
            3 => '3 Comments Written',
            5 => '5 Comments Written',
            10 => '10 Comments Written',
            20 => '20 Comments Written',
            21 => null
        ];

        foreach ($achievements as $key => $message) {
            $user = $this->generateUserComments($key-1);
            $this->assertEquals($message, $this->service->getNextAvailable($user));
        }
    }
}
