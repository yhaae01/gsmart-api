<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AMSTarget;
use App\Models\AMS;

class AMSTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $ams = AMS::all();

        // foreach ($ams as $item) {
        //     AMSTarget::create([
        //         'ams_id' => $item->id,
        //         'year' => '2019',
        //         'target' => rand(1000, 10000)
        //     ]);
        //     AMSTarget::create([
        //         'ams_id' => $item->id,
        //         'year' => '2020',
        //         'target' => rand(1000, 10000)
        //     ]);
        //     AMSTarget::create([
        //         'ams_id' => $item->id,
        //         'year' => '2021',
        //         'target' => rand(1000, 10000)
        //     ]);
        //     AMSTarget::create([
        //         'ams_id' => $item->id,
        //         'year' => '2022',
        //         'target' => rand(1000, 10000)
        //     ]);
        //     AMSTarget::create([
        //         'ams_id' => $item->id,
        //         'year' => '2023',
        //         'target' => rand(1000, 10000)
        //     ]);
        // }
    }
}
