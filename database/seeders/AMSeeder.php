<?php

namespace Database\Seeders;

use App\Models\AM;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            'Iwan Hermawan 523569' => ['initial' => 'IH', 'area_id' => 3],
            'Ayi Kusmana' => ['initial' => 'AK', 'area_id' => 1],
            'Ricky Yanuardi' => ['initial' => 'RY', 'area_id' => 4],
            'Cornelius Bondan' => ['initial' => 'CB', 'area_id' => 4],
            'Suhartono' => ['initial' => 'SH', 'area_id' => 2]
        ];

        foreach ($users as $name => $userData) {
            $user = User::where('name', $name)->first();
    
            if ($user) {
                AM::create([
                    'initial' => $userData['initial'],
                    'area_id' => $userData['area_id'],
                    'user_id' => $user->id,
                ]);
            } else {
                abort(404, 'User with name ' . $name . ' not found.');
            }
        }
    }
}
