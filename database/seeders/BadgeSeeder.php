<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Beginner',
                'totalNumber' => 0,
                'created_at' => now()
            ],
            [
                'name' => 'Intermediate',
                'totalNumber' => 4,
                'created_at' => now()
            ],
            [
                'name' => 'Advanced',
                'totalNumber' => 8,
                'created_at' => now()
            ],
            [
                'name' => 'Master',
                'totalNumber' => 10,
                'created_at' => now()
            ],
        ];

        DB::table('default_badge_achievements')->insert($badges);
    }
}
