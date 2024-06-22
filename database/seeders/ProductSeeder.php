<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Product::create(['name' => 'PBTH']);
        Product::create(['name' => 'Airframe']);
        Product::create(['name' => 'Component']);
        Product::create(['name' => 'Engine & APU']);
        Product::create(['name' => 'Engineering']);
        Product::create(['name' => 'Line']);
        Product::create(['name' => 'Material Trading']);
        Product::create(['name' => 'Training']);
        Product::create(['name' => 'IGTE']);
        Product::create(['name' => 'Other']);
    }
}
