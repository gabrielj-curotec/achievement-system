<?php

namespace App\Services\Achievements;

use App\Events\AchievementUnlocked;
use App\Models\User;
use App\Models\UserAchievement;

abstract class AchievementService
{
    public string $name;

    public string $key;

    protected array $amountRules;

    public function updateUserAchievement(User $user): void
    {
        foreach ($this->amountRules as $key => $amountRule) {
            if ($this->validateRule($key, $this->getAmountToAchievement($user))) {
                $userAchievement = $this->getAchievementByUserAndRule($user, $amountRule);

                if (! $userAchievement) {
                    $this->createAchievement($user, $amountRule);
                    event(new AchievementUnlocked($this->name, $user));
                }
            }
        }
    }

    protected function validateRule(int $currentKey, int $userAmount): bool
    {
        $amountRule = $this->amountRules[$currentKey];
        $nextScore = $this->amountRules[$currentKey + 1] ?? null;

        return (! $nextScore || $userAmount < $nextScore)
            && $userAmount >= $amountRule;
    }

    protected function createAchievement(User $user, int $amountRule): void
    {
        UserAchievement::create([
            'user_id' => $user->id,
            'achievement_key' => $this->key,
            'achievement_message' => $this->getAchievementMessage($amountRule)
        ]);
    }

    protected function getAchievementByUserAndRule(User $user, $amountRule): ?UserAchievement
    {
        return UserAchievement::where('user_id', $user->id)
            ->where('achievement_key', $this->key)
            ->where('achievement_message', $this->getAchievementMessage($amountRule))
            ->first();
    }

    public function getNextAvailable(User $user): ?string
    {
        $currentAchievementsCount = $this->getAmountToAchievement($user);

        foreach ($this->amountRules as $amountRule) {
            $differenceFromCurrent = $currentAchievementsCount - $amountRule;

            if ($differenceFromCurrent < 0) {
                return $this->getAchievementMessage($amountRule);
            }
        }

        return null;
    }

    abstract protected function getAchievementMessage(int $watchedLessons): string;

    abstract protected function getAmountToAchievement(User $user): int;
}
