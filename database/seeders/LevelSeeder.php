<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Level::create([
            'level'       => 1,
            'description' => 'Contract Signing',
        ]);

        Level::create([
            'level'       => 2,
            'description' => 'Attractive Proposal',
        ]);

        Level::create([
            'level'       => 3,
            'description' => 'Opportunity',
        ]);

        Level::create([
            'level'       => 4,
            'description' => 'Awareness',
        ]);
    }
}
