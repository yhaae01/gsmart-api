<?php

namespace Database\Seeders;

use App\Models\Area;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Area::create(['name' => 'Area III']);
        Area::create(['name' => 'Area II']);
        Area::create(['name' => 'Area I']);
        Area::create(['name' => 'KAM GA']);
        Area::create(['name' => 'KAM QG']);
    }
}
