<?php

namespace Database\Seeders;

use App\Models\SalesLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $salesplan = \App\Models\Sales::all();

        foreach ($salesplan as $sales) {
            SalesLevel::create([
                'sales_id' => $sales->id,
                'level_id' => ($sales->type == 'PBTH') ? 1 : 4,
                'status' => 1,
            ]);
        }
    }
}
