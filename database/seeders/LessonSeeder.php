<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessonAchievements = [
            [
                'name' => 'First Lesson Watched',
                'totalNumber' => 1,
            ],
            [
                'name' => '5 Lessons Watched',
                'totalNumber' => 5,
            ],
            [
                'name' => '10 Lessons Watched',
                'totalNumber' => 10,
            ],
            [
                'name' => '25 Lessons Watched',
                'totalNumber' => 25,
            ],
            [
                'name' => '50 Lessons Watched',
                'totalNumber' => 50,
            ],
        ];

        DB::table('default_lesson_achievements')->insert($lessonAchievements);
    }
}
