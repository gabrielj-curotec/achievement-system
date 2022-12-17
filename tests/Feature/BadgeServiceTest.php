<?php

namespace Tests\Feature;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use App\Services\Badges\BadgeService;
use Database\Seeders\BadgeSeeder;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BadgeServiceTest extends TestCase
{
    private BadgeService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = new BadgeService();
    }

    public function testUnlockBeginnerBadge(): void
    {
        $user = User::factory()->create();

        $this->service->updateUserBadge($user);
        $user->refresh();

        $this->assertEquals('Beginner', $user->current_badge->name);
    }

    public function testUnlockIntermediateBadge(): void
    {
        $this->expectsEvents(BadgeUnlocked::class);

        $user = User::factory()->create();
        UserAchievement::factory()->count(4)->create([
            'user_id' => $user->id
        ]);

        $this->service->updateUserBadge($user);
        $user->refresh();

        $this->assertEquals('Intermediate', $user->current_badge->name);
    }

    public function testUnlockAdvancedBadge(): void
    {
        $this->expectsEvents(BadgeUnlocked::class);

        $user = User::factory()->create();
        UserAchievement::factory()->count(8)->create([
            'user_id' => $user->id
        ]);

        $this->service->updateUserBadge($user);
        $user->refresh();

        $this->assertEquals('Advanced', $user->current_badge->name);
    }

    public function testUnlockMasterBadge(): void
    {
        $this->expectsEvents(BadgeUnlocked::class);

        $user = User::factory()->create();
        UserAchievement::factory()->count(10)->create([
            'user_id' => $user->id
        ]);

        $this->service->updateUserBadge($user);
        $user->refresh();

        $this->assertEquals('Master', $user->current_badge->name);
    }

    public function testMustNotUpdateIfNotAchievementsEnough()
    {
        Event::fake();
        $this->seed(BadgeSeeder::class);

        $badges = Badge::all();

        foreach ($badges as $badge) {
            $user = User::factory()->create();
            UserBadge::factory()->create([
                'user_id' => $user->id,
                'badge_id' => Badge::whereName($badge->name)->first()->id,
                'current' => true,
            ]);

            $this->assertEquals($badge->name, $user->current_badge->name);

            UserAchievement::factory()->count($badge->required_achievements+1)->create([
                'user_id' => $user->id
            ]);

            $this->service->updateUserBadge($user);
            $user->refresh();

            $this->assertEquals($badge->name, $user->current_badge->name);
        }

        Event::assertNotDispatched(BadgeUnlocked::class);
    }
}
