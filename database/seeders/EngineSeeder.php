<?php

namespace Database\Seeders;

use App\Models\Engine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EngineSeeder extends Seeder
{

    public function run()
    {
        $csv_file = fopen(base_path("database/data/engines.csv"), "r");

        $first_line = true;
        while (($data = fgetcsv($csv_file, 2000, ",")) !== FALSE) {
            if (!$first_line) {
                Engine::create(['name' => $data['0']]);
            }
            $first_line = false;
        }
        fclose($csv_file);
    }
}
