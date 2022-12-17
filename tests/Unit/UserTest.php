<?php

namespace Tests\Unit;

use App\Models\Badge;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use Tests\TestCase;

class UserTest extends TestCase
{
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testDeactiveCurrentBadge()
    {
        UserBadge::factory()->create([
            'user_id' => $this->user->id,
            'current' => true
        ]);

        $this->user->deactiveBadges();
        $this->user->refresh();

        $this->assertNull($this->user->current_badge);
    }

    public function testGetNextAvailableBadge(): void
    {
        $availableBadges = [
            'Beginner' => 'Intermediate',
            'Intermediate' => 'Advanced',
            'Advanced' => 'Master',
            'Master' => null
        ];

        foreach ($availableBadges as $oldBadge => $newBadge) {
            $this->user->deactiveBadges();
            $this->user->refresh();

            UserBadge::factory()->create([
                'user_id' => $this->user->id,
                'badge_id' => Badge::whereName($oldBadge)->first()->id,
                'current' => true
            ]);

            $this->user->refresh();

            $this->assertEquals($newBadge, optional($this->user->getNextAvailableBadge())->name);
        }
    }

    public function testGetAmountToUnlockNextBadgeToAdvanced(): void
    {
        $user = User::factory()->create();
        $user->deactiveBadges();
        $user->userBadges()->create([
            'badge_id' => Badge::whereName('Intermediate')->first()->id,
            'current' => true
        ]);

        UserAchievement::factory()->count(6)->create([
            'user_id' => $user->id
        ]);

        $user->refresh();

        $this->assertEquals(2, $user->getAmountToUnlockNextBadge());
    }

    public function testGetAmountToUnlockNextBadgeIfAlreadyHadMaster(): void
    {
        $user = User::factory()->create();
        $user->deactiveBadges();
        $user->userBadges()->create([
            'badge_id' => Badge::whereName('Master')->first()->id,
            'current' => true
        ]);

        UserAchievement::factory()->count(10)->create([
            'user_id' => $user->id
        ]);

        $user->refresh();

        $this->assertNull($user->getAmountToUnlockNextBadge());
    }
}
