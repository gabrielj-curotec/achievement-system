<?php

namespace Tests\Unit;

use App\Events\CommentWritten;
use App\Listeners\CommentWrittenListener;
use App\Models\Comment;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CommentWrittenListenerTest extends TestCase
{
    public function testShouldCheckForAchievementWhenTriggered(): void
    {
        $comment = Comment::factory()->create();

        $event = new CommentWritten($comment);
        (new CommentWrittenListener())->handle($event);

        $this->assertEquals(1, $comment->user->userAchievements->count());
        $this->assertEquals(
            'First Comment Written',
            $comment->user->userAchievements[0]->achievement_message
        );
    }

    public function testIsCorrectAttachedToEvent(): void
    {
        Event::fake();
        Event::assertListening(
            CommentWritten::class,
            CommentWrittenListener::class
        );
    }
}
