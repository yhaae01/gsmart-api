<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TMB;

class TMBSeeder extends Seeder
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
        $components = \App\Models\Component::all()->count();
        $engines = \App\Models\Engine::all()->count();
        $apus = \App\Models\Apu::all()->count();
        $maintenances = \App\Models\Maintenance::all()->count();
        $prospects = \App\Models\Prospect::select('id')->whereIn('transaction_type_id', [1,2])->get();

        foreach ($prospects as $prospect) {
            $product = \App\Models\Product::find(rand(1, $products));
            if ($product->name == 'Engine & APU') {
                $ac_type = null;
                $component = null;
                $engine = rand(1, $engines);
                $apu = rand(1, $apus);
            } else if ($product->name = 'Component') {
                $ac_type = null;
                $component = rand(1, $components);
                $engine = null;
                $apu = null;
            } else {
                $ac_type = rand(1, $ac_types);
                $component = null;
                $engine = null;
                $apu = null;
            }

            $tmb = TMB::create([
                'prospect_id' => $prospect->id,
                'product_id' => $product->id,
                'ac_type_id' => $ac_type,
                'component_id' => $component,
                'engine_id' => $engine,
                'apu_id' => $apu,
                'maintenance_id' => rand(1, $maintenances),
                'market_share' => rand(10000, 100000),
                'remarks' => 'This is remarks',
            ]);
        }
    }
}
