<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hangar;

class HangarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Hangar::create(['name' => 'Hangar I']);
        Hangar::create(['name' => 'Hangar II']);
        Hangar::create(['name' => 'Hangar III']);
        Hangar::create(['name' => 'Hangar IV']);
        Hangar::create(['name' => 'Component Shop']);
        Hangar::create(['name' => 'Engine & APU Shop']);
        Hangar::create(['name' => 'IGTE Shop']);
        Hangar::create(['name' => 'Industrial Solution Shop']);
        Hangar::create(['name' => 'Workshop']);
        Hangar::create(['name' => 'Showroom H1']);
        Hangar::create(['name' => 'Showroom H2']);
        Hangar::create(['name' => 'Showroom H3']);
        Hangar::create(['name' => 'Showroom H4']);
        Hangar::create(['name' => 'Other']);
    }
}
