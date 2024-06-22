<?php

namespace Database\Seeders;

use App\Models\SalesUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SalesUpdate::create([
            'sales_id' => 1,
            'detail'   => 'This is Detail',
            'reason'   => 'Success',
        ]);
    }
}
