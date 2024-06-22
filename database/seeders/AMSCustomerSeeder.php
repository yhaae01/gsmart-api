<?php

namespace Database\Seeders;

use App\Imports\AMSCustomerImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class AMSCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = base_path('database/data/ams_customers.xlsx');
        Excel::import(new AMSCustomerImport, $file);

        // $customer = Customer::all();
        // $ams = AMS::all()->count();
        // $areas = Area::pluck('id');

        // foreach ($customer as $item) {
        //     $area = collect($areas)->shuffle()->toArray();
        //     $total = rand(1, 2);

        //     for ($i = 0; $i < $total; $i++) {
        //         AMSCustomer::create([
        //             'customer_id' => $item->id,
        //             'area_id' => $area[$i],
        //             'ams_id' => rand(1, $ams),
        //         ]);
        //     }
        // }
    }
}
