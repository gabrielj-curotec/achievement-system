<?php

namespace App\Services\Achievements;

use App\Models\Lesson;
use App\Models\User;
use App\Models\UserLesson;

class LessonsWatchedAchievementService extends AchievementService
{
    public function __construct()
    {
        $this->name = 'Lessons Watched Achievement';
        $this->amountRules = [1, 5, 10, 25, 50];
        $this->key = 'lessons_watched';
    }

    public function updateOrCreateWithWatchedTrue(User $user, Lesson $lesson): void
    {
        UserLesson::updateOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id
        ], [
            'watched' => true
        ]);
    }

    public function isAlreadyWatched(User $user, Lesson $lesson): bool
    {
        return $user->watchedLessons()->where('id', $lesson->id)->exists();
    }

    protected function getAchievementMessage(int $watchedLessons): string
    {
        if ($watchedLessons == 1) {
            return 'First Lesson Watched';
        }

        return "$watchedLessons Lessons Watched";
    }

    protected function getAmountToAchievement(User $user): int
    {
        return $user->watched_lessons_count;
    }
}
