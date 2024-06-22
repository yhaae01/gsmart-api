<?php

namespace Database\Seeders;

use App\Models\IGTE;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IgteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IGTE::create(['name' => 'Turbine Field Services']);
        IGTE::create(['name' => 'Turbine Part Repair']);
        IGTE::create(['name' => 'Turbine Retail']);
        IGTE::create(['name' => 'Generator Field Services']);
        IGTE::create(['name' => 'Generator Shop Repair']);
        IGTE::create(['name' => 'Generator Part Retail']);
        IGTE::create(['name' => 'Other']);
    }
}
