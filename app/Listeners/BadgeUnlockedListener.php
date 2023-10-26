<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Models\DefaultBadgeAchievement;


class BadgeUnlockedListener
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
    public function handle(BadgeUnlocked $event): void
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
