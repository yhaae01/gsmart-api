<?php

namespace Database\Seeders;

use App\Models\Apu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_file = fopen(base_path("database/data/apu.csv"), "r");

        $first_line = true;
        while (($data = fgetcsv($csv_file, 2000, ",")) !== FALSE) {
            if (!$first_line) {
                Apu::create(['name' => $data['0']]);
            }
            $first_line = false;
        }
        fclose($csv_file);
    }
}
