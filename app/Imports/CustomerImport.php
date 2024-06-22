<?php

namespace App\Imports;

use App\Models\Region;
use App\Models\Countries;
use App\Models\Customer;
use App\Models\CustomerSwift;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomerImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            DB::beginTransaction();

            $customerSwiftCode = trim($row['customer_code']);
            $customerSwiftName = trim($row['customer_name']);
            $customerCode = trim($row['group_code']);
            $customerName = trim($row['group_name']);
            $countryName = trim($row['country']);
            $regionName = trim($row['region']);
            $groupType = ($customerCode == 'GIA0') ? 0 : 1;

            $region = Region::firstWhere('name', $regionName);
            if (!$region) {
                $region = new Region(['name' => $regionName]);
                $region->save();
            }

            $country = Countries::where('name', $countryName)
                                ->where('region_id', $region->id)
                                ->first(); 
            if (!$country) {
                $country = new Countries([
                    'name' => $countryName,
                    'region_id' => $region->id
                ]);
                $country->save();
            }

            $customer = Customer::where('code', $customerCode)
                                ->where('name', $customerName)
                                ->where('group_type', $groupType)
                                ->where('country_id', $country->id)
                                ->first();
            if (!$customer) {
                $customer = new Customer([
                    'code' => $customerCode,
                    'name' => $customerName,
                    'group_type' => $groupType,
                    'country_id' => $country->id
                ]);
                $customer->save();
            }

            $customerSwift = new CustomerSwift([
                'code' => $customerSwiftCode,
                'name' => $customerSwiftName,
                'customer_group_id' => $customer->id
            ]);
            $customerSwift->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
        }
    }
}
