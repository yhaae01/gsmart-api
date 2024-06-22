<?php

namespace Database\Seeders;

use App\Models\Prospect;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProspectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ams_customer = \App\Models\AMSCustomer::all();
        $t_type = \App\Models\TransactionType::all()->count();
        $p_type = \App\Models\ProspectType::all()->count();
        $strategic = \App\Models\StrategicInitiatives::all()->count();
        $pm = \App\Models\User::all()->count();

        $years = [2019, 2020, 2021, 2022];

        foreach ($ams_customer as $item) {
            $years = collect($years)->shuffle()->toArray();
            $total = rand(1,4);

            for ($i = 0; $i < $total; $i++) {
                $p_prospect = rand(1, $p_type);
                if ($p_prospect == 1) {
                    $d_strategic = null;
                    $d_pm = null;
                } else {
                    $d_strategic = rand(1, $strategic);
                    $d_pm = rand(1, $pm);
                }

                $t_prospect = rand(1, $t_type);
                if ($t_prospect == 3) {
                    for ($j = 0; $j < 12; $j++) {
                        Prospect::create([
                            'year' => $years[$i],
                            'transaction_type_id' => $t_prospect,
                            'prospect_type_id' => $p_prospect,
                            'strategic_initiative_id' => $d_strategic,
                            'pm_id' => $d_pm,
                            'ams_customer_id' => $item->id,
                        ]);
                    }
                } else {
                    Prospect::create([
                        'year' => $years[$i],
                        'transaction_type_id' => $t_prospect,
                        'prospect_type_id' => $p_prospect,
                        'strategic_initiative_id' => $d_strategic,
                        'pm_id' => $d_pm,
                        'ams_customer_id' => $item->id,
                    ]);
                }
            }
        }
    }
}
