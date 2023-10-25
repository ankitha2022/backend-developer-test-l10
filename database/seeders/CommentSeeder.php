<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $commentAchievements = [
            [
                'name' => 'First Comment Written',
                'totalNumber' => 1,
            ],
            [
                'name' => '3 Comments Written',
                'totalNumber' => 3,
            ],
            [
                'name' => '5 Comments Written',
                'totalNumber' => 5,
            ],
            [
                'name' => '10 Comments Written',
                'totalNumber' => 10,
            ],
            [
                'name' => '20 Comments Written',
                'totalNumber' => 20,
            ],
        ];

        DB::table('default_comment_achievements')->insert($commentAchievements);
    }
}
