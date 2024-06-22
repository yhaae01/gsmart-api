<?php

namespace Database\Seeders;

use App\Imports\CustomerImport;
use Illuminate\Database\Seeder;
use App\Models\Countries;
use Maatwebsite\Excel\Facades\Excel;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = base_path("database/data/customer_with_group_country_region.xlsx");
        Excel::import(new CustomerImport, $file);

        // $countries = Countries::all()->count();

        // $csv_file = fopen(base_path("database/data/customers.csv"), "r");

        // $first_line = true;
        // while (($data = fgetcsv($csv_file, 2000, ";")) !== FALSE) {
        //     if (!$first_line) {
        //         Customer::create([
        //             'name'       => $data['0'],
        //             // 'code'       => Str::upper(Str::random(6)),
        //             'group_type' => $data['1'],
        //             // 'country_id' => rand(1, $countries),
        //         ]);
        //     }
        //     $first_line = false;
        // }
        // fclose($csv_file);
    }
}
