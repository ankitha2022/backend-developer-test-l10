<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;

class AchievementsTest extends TestCase
{
  
    public function testUserHasNoAchievements()
    {
        $user = User::factory()->create();
    
        $response = $this->actingAs($user)->get('/users/' . $user->id . '/achievements');
    
        $response->assertStatus(200);
        $response->assertExactJson([
            'unlocked_achievements' => [],
            'next_available_achievements' => ["First Lesson Watched", "First Comment Written"],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 4,
        ]);
    }
    
}
