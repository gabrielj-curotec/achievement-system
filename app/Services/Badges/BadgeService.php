<?php

namespace App\Services\Badges;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Support\Collection;

class BadgeService
{
    public function updateUserBadge(User $user): void
    {
        $currentAchievementsCount = $user->userAchievements->count();

        $badges = $this->getAllBadges();
        $realCurrentBadge = $this->getRealCurrentBadge($badges, $currentAchievementsCount);

        if (! $this->mustUpdateBadge($user, $realCurrentBadge)) {
            return;
        }

        $user->deactiveBadges();

        $user->userBadges()->create([
            'badge_id' => $realCurrentBadge->id,
            'current' => true
        ]);

        event(new BadgeUnlocked($user->current_badge->name, $user));
    }

    private function mustUpdateBadge(User $user, ?Badge $realCurrentBadge): bool
    {
        return $realCurrentBadge != $user->current_badge;
    }

    private function getRealCurrentBadge(Collection $badges, int $currentAchievementsCount): Badge
    {
        foreach ($badges as $key => $badge) {
            $currentValue = $badge->required_achievements;
            $differenceFromCurrent = $currentAchievementsCount - $currentValue;

            if ($differenceFromCurrent > 0) {
                continue;
            }

            return $differenceFromCurrent < 0
                ? $badges[$key - 1]
                : $badge;
        }

        return $badges->last();
    }

    public function getAllBadges(): Collection
    {
        return Badge::orderBy('required_achievements')->get();
    }
}
