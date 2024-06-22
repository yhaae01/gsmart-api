<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CancelCategory as Category;

class CancelCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create(['name' => 'Pricing']);
        Category::create(['name' => 'Reschedule']);
        Category::create(['name' => 'Missed Plan']);
        Category::create(['name' => 'Capacity Capability']);
        Category::create(['name' => 'Customer Financial']);
        Category::create(['name' => 'Internal Customer']);
        Category::create(['name' => 'Other']);
    }
}
