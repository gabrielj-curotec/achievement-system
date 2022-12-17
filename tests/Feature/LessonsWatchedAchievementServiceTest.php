<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Models\Lesson;
use App\Services\Achievements\LessonsWatchedAchievementService;
use App\Models\User;
use App\Models\UserLesson;
use Tests\TestCase;

class LessonsWatchedAchievementServiceTest extends TestCase
{
    private LessonsWatchedAchievementService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new LessonsWatchedAchievementService();
    }

    public function testIsAlreadyWatched(): void
    {
        $userLesson = UserLesson::factory()->create([
            'watched' => true
        ]);

        $this->assertTrue($this->service->isAlreadyWatched($userLesson->user, $userLesson->lesson));
    }

    public function testIsNotAlreadyWatched(): void
    {
        $userLesson = UserLesson::factory()->create([
            'watched' => false
        ]);

        $this->assertFalse($this->service->isAlreadyWatched($userLesson->user, $userLesson->lesson));
    }

    private function generateUserLessons(int $amount): array
    {
        $user = User::factory()->create();
        UserLesson::factory()->count($amount)->create([
            'user_id' => $user->id,
            'watched' => true
        ]);

        $lesson = Lesson::factory()->create();

        return [$user, $lesson];
    }

    public function testUnlockFirstLessonWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $this->service->updateOrCreateWithWatchedTrue($user, $lesson);
        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => 'First Lesson Watched'
        ]);
    }

    public function testUnlock5LessonsWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        [$user, $lesson] = $this->generateUserLessons(4);

        $this->service->updateOrCreateWithWatchedTrue($user, $lesson);
        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => '5 Lessons Watched'
        ]);
    }

    public function testUnlock10LessonsWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        [$user, $lesson] = $this->generateUserLessons(9);

        $this->service->updateOrCreateWithWatchedTrue($user, $lesson);
        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => '10 Lessons Watched'
        ]);
    }

    public function testUnlock25LessonsWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        [$user, $lesson] = $this->generateUserLessons(24);

        $this->service->updateOrCreateWithWatchedTrue($user, $lesson);
        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => '25 Lessons Watched'
        ]);
    }

    public function testUnlock50LessonsWatchedAchievement(): void
    {
        $this->expectsEvents(AchievementUnlocked::class);

        [$user, $lesson] = $this->generateUserLessons(49);

        $this->service->updateOrCreateWithWatchedTrue($user, $lesson);
        $this->service->updateUserAchievement($user);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_key' => $this->service->key,
            'achievement_message' => '50 Lessons Watched'
        ]);
    }

    public function testGetNextAvailableAchievements(): void
    {
        $achievements = [
            1 => 'First Lesson Watched',
            5 => '5 Lessons Watched',
            10 => '10 Lessons Watched',
            25 => '25 Lessons Watched',
            50 => '50 Lessons Watched',
            51 => null
        ];

        foreach ($achievements as $key => $message) {
            [$user] = $this->generateUserLessons($key-1);
            $this->assertEquals($message, $this->service->getNextAvailable($user));
        }
    }
}
