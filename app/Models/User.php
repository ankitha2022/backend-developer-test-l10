<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched()
    {
        return $this->belongsToMany(Lesson::class)->wherePivot('watched', true);
    }

    /**
     * Define the many-to-many relationship with lessons.
     *
     * @return BelongsToMany
     */
    public function lessonAchievements()
    {
        return $this->belongsToMany(DefaultLessonAchievement::class, 'user_lesson_achievements');
    }

     /**
     * Check if the user has a lesson achievement by name.
     *
     * @param string $achievementName
     * @return bool
     */
    public function hasLessonAchievement($achievementName)
    {
        return $this->lessonAchievements()->where('name', $achievementName)->exists();
    }

     /**
     * Increment the user's lesson count.
     */
    public function incrementLessonCount()
    {
        $this->lesson_count++;
        $this->save();
    }

     /**
     * Unlock a lesson achievement for the user.
     *
     * @param string $achievementName
     */
    public function unlockLessonAchievement($achievementName)
    {
        $achievement = DefaultLessonAchievement::where('name', $achievementName)->first();

        if ($achievement) {
            $this->lessonAchievements()->attach($achievement);
        }
    } 


    /**
     * Define the many-to-many relationship with comment achievements.
     *
     * @return BelongsToMany
     */
    public function commentAchievements()
    {
        return $this->belongsToMany(DefaultCommentAchievement::class, 'user_comment_achievements');
    }

    /**
     * Check if the user has a comment achievement by name.
     *
     * @param string $achievementName
     * @return bool
     */
    public function hasCommentAchievement($achievementName)
    {
        return $this->commentAchievements()->where('name', $achievementName)->exists();
    }

    /**
     * Increment the user's comment count.
     */
    public function incrementCommentCount()
    {
        $this->comment_count++;
        $this->save();
    }

    /**
     * Unlock a comment achievement for the user.
     *
     * @param string $achievementName
     */
    public function unlockCommentAchievement($achievementName)
    {
        $achievement = DefaultCommentAchievement::where('name', $achievementName)->first();

        if ($achievement) {
            $this->commentAchievements()->attach($achievement);
        }
    }

    public function badgeAchievements()
    {
        return $this->belongsToMany(DefaultBadgeAchievement::class, 'user_badge_achievements');
    }
    
    public function calculateBadge()
    {
        $unlockedAchievementsCount = $this->unlockedAchievements->count();
        
        // Check if the user has enough achievements for a badge.
        if ($unlockedAchievementsCount >= 4) {
            $badge = DefaultBadgeAchievement::where('totalNumber', $unlockedAchievementsCount)->first();
            
            if ($badge) {
                $this->unlockBadge($badge->name);
                event(new BadgeUnlocked($badge->name, $this));
            }
        }
    }

    /**
     * Get unlocked achievements for the user.
     *
     * @return array
     */
    public function getUnlockedAchievements()
    {
        // Fetch unlocked lesson achievements
        $lessonAchievements = $this->lessonAchievements->pluck('name')->toArray();

        // Fetch unlocked comment achievements
        $commentAchievements = $this->commentAchievements->pluck('name')->toArray();

        // Combine and return both lesson and comment achievements
        return array_merge($lessonAchievements, $commentAchievements);
    }

    /**
     * Get next available achievements for the user.
     *
     * @return array
     */
    public function getNextAvailableAchievements()
    {
        // Fetch the user's unlocked achievements
        $unlockedAchievements = $this->getUnlockedAchievements();

        // Define an array to store the next available achievements
        $nextAvailableAchievements = [];

        // Group the achievements into lesson and comment achievements
        $lessonAchievements = DefaultLessonAchievement::all();
        $commentAchievements = DefaultCommentAchievement::all();

        // Loop through lesson achievements
        foreach ($lessonAchievements as $achievement) {
            if (!in_array($achievement->name, $unlockedAchievements)) {
                $nextAvailableAchievements[] = $achievement->name;
                break; // Break after adding the next lesson achievement.
            }
        }

        // Loop through comment achievements
        foreach ($commentAchievements as $achievement) {
            if (!in_array($achievement->name, $unlockedAchievements)) {
                $nextAvailableAchievements[] = $achievement->name;
                break; // Break after adding the next comment achievement.
            }
        }

        return $nextAvailableAchievements;
    }


    /**
     * Get the current badge for the user.
     *
     * @return string
     */
    public function getCurrentBadge()
    {
    $unlockedAchievementsCount = $this->getUnlockedAchievements();

    // Determine the user's current badge based on their unlocked achievements.
    $badge = DefaultBadgeAchievement::where('totalNumber', '<=', count($unlockedAchievementsCount))
    ->orderBy('totalNumber', 'desc')
    ->first();

    return $badge ? $badge->name : 'Beginner'; // Default to Beginner if no badge is found.
    }

    public function remainingToUnlockNextBadge()
    {
        // Calculate the number of achievements needed to unlock the next badge
        $unlockedAchievementsCount = count($this->getUnlockedAchievements());
        
        // Find the next badge
        $nextBadge = DefaultBadgeAchievement::where('totalNumber', '>', $unlockedAchievementsCount)
            ->orderBy('totalNumber')
            ->first();

        if ($nextBadge) {
            $remainingAchievementsNeeded = $nextBadge->totalNumber - $unlockedAchievementsCount;
            return $remainingAchievementsNeeded;
        }

        return 0; // No next badge found, the user may have unlocked all badges.
    }

    /**
     * Get the next badge the user can earn.
     *
     * @return string
     */
    public function getNextBadge()
    {
        $unlockedAchievementsCount = $this->getUnlockedAchievements();

        // Determine the next badge the user can earn based on their unlocked achievements.
        $nextBadge = DefaultBadgeAchievement::where('totalNumber', '>', count($unlockedAchievementsCount))
            ->orderBy('totalNumber')
            ->first();

        return $nextBadge ? $nextBadge->name : 'Master'; // Default to Master if no next badge is found.
    }


    /**
     * Get the achievement name to unlock based on lesson count.
     *
     * @return string|null
     */
    public function lessonCountAchievementName()
    {
        $lessonCount = $this->watched()->count();

        // Get the predefined lesson achievements
        $lessonAchievements = DefaultLessonAchievement::all();

        foreach ($lessonAchievements as $achievement) {
            if ($lessonCount >= $achievement->totalNumber) {
                return $achievement->name;
            }
        }

        return null;
    }

    /**
     * Get the achievement name to unlock based on comment count.
     *
     * @return string|null
     */
    public function commentCountAchievementName()
    {
        $commentCount = $this->comments()->count();

        // Get the predefined comment achievements
        $commentAchievements = DefaultCommentAchievement::all();

        foreach ($commentAchievements as $achievement) {
            if ($commentCount >= $achievement->totalNumber) {
                return $achievement->name;
            }
        }

        return null;
    }

}

