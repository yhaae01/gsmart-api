<?php

namespace Database\Seeders;

use App\Models\PBTH;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PBTHSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = \App\Models\Product::all()->count();
        $ac_types = \App\Models\AircraftType::all()->count();
        $prospects = \App\Models\Prospect::select('id')->where('transaction_type_id', 3)->get();

        $i = 1;
        foreach ($prospects as $prospect) {
            if ($i == 13) {
                $i = 1;
            }
            $month = Carbon::create()->day(1)->month($i);

            $pbth = PBTH::create([
                'prospect_id' => $prospect->id,
                'product_id' => rand(1, $products),
                'ac_type_id' => rand(1, $ac_types),
                'month' => $month->format('F'),
                'rate' => rand(10, 90),
                'flight_hour' => rand(1000, 10000),
                'market_share' => rand(10000, 100000),
            ]);

            $i++;
        }
    }
}
