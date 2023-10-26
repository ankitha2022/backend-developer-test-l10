<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CommentWritten;
use App\Models\User;
use App\Models\DefaultCommentAchievement;
use App\Models\UserCommentAchievement;

class CommentWrittenListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentWritten $event)
    {
        $user = $event->comment->user;
        // Increment the user's comment count.
        $user->incrementCommentCount();

        // Check if the user has unlocked new comment achievements based on their comment count.
        $commentCountAchievementName = $user->commentCountAchievementName();

        if ($commentCountAchievementName) {
            // Unlock the comment achievement for the user.
            $user->unlockCommentAchievement($commentCountAchievementName);
            event(new AchievementUnlocked($commentCountAchievementName, $user));
            $user->calculateBadge();
        }
        
    }
    
}
