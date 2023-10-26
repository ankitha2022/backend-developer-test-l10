<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\LessonWatched;
use App\Models\User;
use App\Models\DefaultLessonAchievement;
use App\Models\UserLessonAchievement;

class LessonWatchedListener
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
   public function handle(LessonWatched $event)
    {
        $user = $event->user;
        $lesson = $event->lesson;
    
        // Increment the user's watched lessons count.
        $user->incrementLessonCount();

        // Check if the user has unlocked new lesson achievements based on their watched lesson count.
        $lessonCountAchievementName = $user->lessonCountAchievementName();

        if ($lessonCountAchievementName) {
            // Unlock the lesson achievement for the user.
            $user->unlockLessonAchievement($lessonCountAchievementName);
            event(new AchievementUnlocked($lessonCountAchievementName, $user));
            $user->calculateBadge();
        }
        
    }

}
