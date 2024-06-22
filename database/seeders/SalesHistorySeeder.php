<?php

namespace Database\Seeders;

use App\Models\SalesHistory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SalesHistory::create([
            'sales_id' => 1,
            'detail'   => 'This is Detail',
        ]);
    }
}
