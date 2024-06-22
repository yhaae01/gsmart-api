<?php

namespace Database\Seeders;

use App\Models\Learning;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Learning::create(['name' => 'Rating /147']);
        Learning::create(['name' => 'MRO /145']);
        Learning::create(['name' => 'GSE /139']);
        Learning::create(['name' => 'Other']);
    }
}