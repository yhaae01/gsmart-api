<?php

namespace Database\Seeders;

use App\Models\ProspectType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProspectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProspectType::create([
            'name'        => 'Organic',
            'description' => '-'
        ]);
        ProspectType::create([
            'name'        => 'In Organic',
            'description' => '-'
        ]);
    }
}
