<?php

namespace Database\Seeders;

use App\Models\AircraftType as ACType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AircraftTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_file = fopen(base_path("database/data/aircraft_types.csv"), "r");

        $first_line = true;
        while (($data = fgetcsv($csv_file, 2000, ",")) !== FALSE) {
            if (!$first_line) {
                ACType::create(['name' => $data['0']]);
            }
            $first_line = false;
        }
        fclose($csv_file);
    }
}
