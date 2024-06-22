<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Administrator
        $admin = User::create([
            'name'              => 'Superadmin',
            'username'          => 'administrator',
            'nopeg'             => '111',
            'unit'              => 'TDI',
            'role_id'           => 1,
            'email'             => null,
            'password'          => Hash::make('p@ssw0rd'),
            'email_verified_at' => null,
        ]);
        $admin->assignRole('Administrator');

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
                        $email = $data[3];
                        $unit = $data[4];
                        $role = $data[5];
                        $am = $data[6];

                        $user = User::create([
                            'name'              => $name,
                            'username'          => $nopeg,
                            'nopeg'             => $nopeg,
                            'unit'              => $unit,
                            'role_id'           => $role,
                            'email'             => $email,
                            'password'          => null,
                            'email_verified_at' => null,
                        ]);
                        $user->assignRole(User::ROLES[$user->role_id]);

                        \Artisan::call("ldap:import", [
                            'provider' => 'users',
                            'user' => $user->nopeg,
                            '--no-interaction',
                        ]);

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
}
