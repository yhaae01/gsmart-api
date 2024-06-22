<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Maintenance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaintenanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_file = fopen(base_path("database/data/product_maintenance.csv"), "r");

        $first_line = true;
        while (($data = fgetcsv($csv_file, 2000, ",")) !== FALSE) {
            if (!$first_line) {
                $maintenance = trim($data['0']);
                $product = trim($data['1']);

                $exists_product = Product::firstWhere('name', $product);

                if (!$exists_product) {
                    $exists_product = Product::firstOrNew(['name' => 'Other']);
                }

                Maintenance::create([
                    'name' => $maintenance,
                    'product_id' => $exists_product->id
                ]);
            }
            $first_line = false;
        }
        fclose($csv_file);
    }
}
