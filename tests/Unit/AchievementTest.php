<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AchievementTest extends TestCase
{
    public function test_user_can_unlock_achievements()
    {
        $user = User::factory()->create();
        
        // Unlock some lesson achievements
        $user->unlockLessonAchievement('First Lesson Watched');
        $user->unlockLessonAchievement('10 Lessons Watched');
        
        // Unlock some comment achievements
        $user->unlockCommentAchievement('First Comment Written');
        $user->unlockCommentAchievement('10 Comments Written');
        
        // Check if the achievements are unlocked
        $this->assertTrue($user->hasLessonAchievement('First Lesson Watched'));
        $this->assertTrue($user->hasLessonAchievement('10 Lessons Watched'));
        $this->assertTrue($user->hasCommentAchievement('First Comment Written'));
        $this->assertTrue($user->hasCommentAchievement('10 Comments Written'));
    }

    public function test_user_can_calculate_badge()
    {
        $user = User::factory()->create();
        
        // Unlock 8 achievements (Intermediate level)
        $achievements = ['First Lesson Watched', '10 Lessons Watched', 'First Comment Written', '10 Comments Written'];
       
        foreach ($achievements as $achievement) {
            if (strpos($achievement, 'Watched') !== false) {
                $user->unlockLessonAchievement($achievement);
            } elseif (strpos($achievement, 'Written') !== false) {
                $user->unlockCommentAchievement($achievement);
            }
        }
        
        $user->calculateBadge();
        $this->assertTrue($user->hasBadge('Intermediate'));
    }

    public function test_user_does_not_receive_badge_without_enough_achievements()
    {
        $user = User::factory()->create();
        
        // Unlock only 3 achievements (Intermediate level requires 4)
        $achievements = ['First Lesson Watched', 'First Comment Written', '3 Comments Written'];
        foreach ($achievements as $achievement) {
            $user->unlockLessonAchievement($achievement);
        }
        
        $user->calculateBadge();
        
        $this->assertFalse($user->hasBadge('Intermediate'));
    }

    public function test_achievements_controller_returns_achievements()
    {
        $user = User::factory()->create();
        
        // Unlock some achievements for the user
        $user->unlockLessonAchievement('First Lesson Watched');
        $user->unlockCommentAchievement('First Comment Written');
        
        // Request to the achievements endpoint
        $response = $this->get("/users/{$user->id}/achievements");
        
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Lesson Watched', 'First Comment Written'],
                'next_available_achievements' => ['5 Lessons Watched', '3 Comments Written'],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
            ]);
    }

    public function test_user_can_unlock_additional_achievements()
    {
        $user = User::factory()->create();
        
        // Unlock some lesson achievements
        $user->unlockLessonAchievement('First Lesson Watched');
        $user->unlockLessonAchievement('5 Lessons Watched');
        
        // Unlock some comment achievements
        $user->unlockCommentAchievement('First Comment Written');
        $user->unlockCommentAchievement('3 Comments Written');
        
        // Check if the achievements are unlocked
        $this->assertTrue($user->hasLessonAchievement('5 Lessons Watched'));
        $this->assertTrue($user->hasCommentAchievement('3 Comments Written'));
        
        // Recalculate the badge
        $user->calculateBadge();
        
        // Check if the user now has an 'Intermediate' badge
        $this->assertTrue($user->hasBadge('Intermediate'));
    }

    public function test_user_can_upgrade_badge_to_advanced()
    {
        $user = User::factory()->create();
        
        // Unlock achievements to reach the 'Advanced' badge
        $achievements = ['First Lesson Watched','5 Lessons Watched', '10 Lessons Watched','25 Lessons Watched', 'First Comment Written','3 Comments Written', '5 Comments Written','10 Comments Written'];
        foreach ($achievements as $achievement) {
            if (strpos($achievement, 'Watched') !== false) {
                $user->unlockLessonAchievement($achievement);
            } elseif (strpos($achievement, 'Written') !== false) {
                $user->unlockCommentAchievement($achievement);
            }
        }
        
        $user->calculateBadge();
        
        // Check if the user now has an 'Advanced' badge
        $this->assertTrue($user->hasBadge('Advanced'));
    }
}
