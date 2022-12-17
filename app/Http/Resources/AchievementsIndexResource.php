<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class AchievementsIndexResource extends JsonResource
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'unlocked_achievements' => $this->getUnlockedAchievements(),
            'next_available_achievements' => $this->user->getNextAvailableAchievements(),
            'current_badge' => optional($this->user->current_badge)->name,
            'next_badge' => optional($this->user->getNextAvailableBadge())->name,
            'remaing_to_unlock_next_badge' => $this->user->getAmountToUnlockNextBadge()
        ];
    }

    private function getUnlockedAchievements(): Collection
    {
        return $this->user->userAchievements
            ->unique('achievement_message')
            ->pluck('achievement_message');
    }
}
