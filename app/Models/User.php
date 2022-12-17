<?php

namespace App\Models;

use App\Models\Comment;
use App\Services\Achievements\CommentsWrittenAchievementsService;
use App\Services\Achievements\LessonsWatchedAchievementService;
use App\Services\Badges\BadgeService;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    public const AVAILABLE_ACHIEVEMENTS = [
        CommentsWrittenAchievementsService::class,
        LessonsWatchedAchievementService::class
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function userLessons()
    {
        return $this->hasMany(UserLesson::class);
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'user_lessons', 'user_id', 'lesson_id');
    }

    public function watchedLessons()
    {
        return $this->lessons()->wherePivot('watched', true);
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges', 'user_id', 'badge_id');
    }

    public function getWatchedLessonsCountAttribute(): int
    {
        return $this->watchedLessons()->count();
    }

    public function getCurrentBadgeAttribute(): ?Badge
    {
        return optional(
            $this->userBadges()
                ->where('current', true)
                ->latest()
                ->first()
        )->badge;
    }

    public function deactiveBadges(): void
    {
        $this->userBadges()->where('current', true)->update(['current' => false]);
    }

    public function getNextAvailableAchievements(): array
    {
        $availableAchievements = [];

        foreach (self::AVAILABLE_ACHIEVEMENTS as $achievementService) {
            $instance = new $achievementService();

            $availableAchievements[$instance->name] = $instance->getNextAvailable($this);
        }

        return $availableAchievements;
    }

    public function getNextAvailableBadge(): ?Badge
    {
        $badgeService = new BadgeService();
        $badges = $badgeService->getAllBadges();

        foreach ($badges as $key => $badge) {
            if ($badge->id == $this->current_badge->id) {
                return $badges[$key + 1] ?? null;
            }
        }

        return null;
    }

    public function getAmountToUnlockNextBadge(): ?int
    {
        $nextBadge = $this->getNextAvailableBadge();

        if (! $nextBadge) {
            return null;
        }

        $amount = $nextBadge->required_achievements - $this->userAchievements->count();

        if ($amount < 0) {
            return null;
        }

        return $amount;
    }
}
