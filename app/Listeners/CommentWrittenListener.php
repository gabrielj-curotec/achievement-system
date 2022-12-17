<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Services\Achievements\CommentsWrittenAchievementsService;

class CommentWrittenListener
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\CommentWritten  $event
     * @return void
     */
    public function handle(CommentWritten $event)
    {
        $service = app(CommentsWrittenAchievementsService::class);
        $service->updateUserAchievement($event->comment->user);
    }
}
