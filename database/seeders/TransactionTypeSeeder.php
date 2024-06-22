<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionType::create([
            'name' => 'TMB Retail',
            'description' => 'Time Material Based - Retail',
        ]);
        TransactionType::create([
            'name' => 'TMB Project',
            'description' => 'Time Material Based - Project'
        ]);
        TransactionType::create([
            'name' => 'PBTH',
            'description' => 'Power By The Hours',
        ]);
    }
}
