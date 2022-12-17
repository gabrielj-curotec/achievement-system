<?php

namespace App\Services\Achievements;

use App\Models\User;

class CommentsWrittenAchievementsService extends AchievementService
{
    public function __construct()
    {
        $this->name = 'Comments Written Achievements';
        $this->amountRules = [1, 3, 5, 10, 20];
        $this->key = 'comments_written';
    }

    protected function getAchievementMessage(int $commentsWritten): string
    {
        if ($commentsWritten == 1) {
            return 'First Comment Written';
        }

        return "$commentsWritten Comments Written";
    }

    protected function getAmountToAchievement(User $user): int
    {
        return $user->comments->count();
    }
}
