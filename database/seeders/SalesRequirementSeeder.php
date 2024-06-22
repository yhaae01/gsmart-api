<?php

namespace Database\Seeders;

use App\Models\SalesRequirement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalesRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $salesplans = \App\Models\Sales::all();
        $requirements = \App\Models\Requirement::all();

        foreach ($salesplans as $sales) {
            foreach ($requirements as $requirement) {
                if ($sales->type == 'PBTH') {
                    $status = ($requirement->id != 9) ? 1 : 0;
                } else {
                    $status = in_array($requirement->id, [1,5]) ? 1 : 0;
                }

                SalesRequirement::create([
                    'sales_id' => $sales->id,
                    'requirement_id' => $requirement->id,
                    'status' => $status,
                ]);
            }
        }
    }
}
