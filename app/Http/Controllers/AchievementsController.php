<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
         // Fetch unlocked achievements for the user
        $unlockedAchievements = $user->getUnlockedAchievements();

        // Fetch next available achievements for the user
        $nextAvailableAchievements = $user->getNextAvailableAchievements();

        // Calculate the current badge and the next badge
        $currentBadge = $user->getCurrentBadge();
        $nextBadge = $user->getNextBadge();

        // Calculate the number of additional achievements needed for the next badge
        $remainingToUnlockNextBadge = $user->remainingToUnlockNextBadge();

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge,
        ]);

    }
}
