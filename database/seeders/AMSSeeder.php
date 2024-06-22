<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AM;
use App\Models\AMS;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $excel_file = base_path("database/data/user.xlsx");

        try {
            $spreadsheet = IOFactory::load($excel_file);
            $worksheet = $spreadsheet->getActiveSheet();

            $first_row = true;
            foreach ($worksheet->getRowIterator() as $row) {
                if (!$first_row) {
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false); // Loop through all cells, even if empty

                    $data = [];
                    foreach ($cellIterator as $cell) {
                        $data[] = $cell->getValue();
                    }

                    // Process data
                    try {
                        DB::beginTransaction();

                        $name = $data[0];
                        $initial = $data[1];
                        $nopeg = $data[2];
                        $role = $data[5];
                        $am = $data[6];

                        if (in_array($role, [6, 7])) {
                            $user = User::firstWhere('nopeg', $nopeg);
                            $am = AM::firstWhere('initial', $am);

                            AMS::create([
                                'user_id' => $user->id,
                                'initial' => $initial,
                                'am_id' => $am?->id
                            ]);
                        }

                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollback();
                        dd($e);
                    }
                }
                $first_row = false;
            }
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            dd($e);
        }
    }
    
    // public function run()
    // {
    //     $csv_file = fopen(base_path("database/data/user.csv"), "r");

    //     $first_line = true;
    //     while (($data = fgetcsv($csv_file, 2000, ";")) !== FALSE) {
    //         if (!$first_line) {
    //             try {
    //                 DB::beginTransaction();
    //                 $name = $data['0'];
    //                 $initial = $data['1'];
    //                 $nopeg = $data['2'];
    //                 $email = $data['3'];
    //                 $unit = $data['4'];
    //                 $role = $data['5'];

    //                 $user = User::create([
    //                     'name'              => $name,
    //                     'username'          => $nopeg,
    //                     'nopeg'             => $nopeg,
    //                     'unit'              => $unit,
    //                     'role_id'           => $role,
    //                     'email'             => $email,
    //                     'password'          => null,
    //                     'email_verified_at' => null,
    //                 ]);

    //                 AMS::create([
    //                     'user_id' => $user->id,
    //                     'initial' => $initial,
    //                 ]);

    //                 \Artisan::call("ldap:import", [
    //                     'provider' => 'users',
    //                     'user' => $user->nopeg,
    //                     '--no-interaction',
    //                 ]);

    //                 DB::commit();
    //             } catch (\Exception $e) {
    //                 DB::rollback();

    //                 dd($e);
    //             }
    //         }
    //         $first_line = false;
    //     }
    //     fclose($csv_file);
    // }
}
