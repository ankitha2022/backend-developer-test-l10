<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\BadgeUnlocked;
use App\Models\User;

class BadgeUnlockedListener
{
     /**
     * Handle the event.
     */
    public function handle(BadgeUnlocked $event)
    {
        $badgeName = $event->badgeName;
        $user = $event->user;

        // Attach the unlocked badge achievement to the user.
        $badgeAchievement = DefaultBadgeAchievement::where('name', $badgeName)->first();

        if ($badgeAchievement) {
            $user->badgeAchievements()->attach($badgeAchievement);
        }
    }
}
