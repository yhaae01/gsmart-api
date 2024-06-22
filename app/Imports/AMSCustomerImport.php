<?php

namespace App\Imports;

use App\Models\AMSCustomer;
use App\Models\Customer;
use App\Models\AMS;
use App\Models\Area;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AMSCustomerImport implements ToModel, WithHeadingRow
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

            $customerCode = trim($row['code']);
            $customerName = trim($row['customer']);
            $area = trim($row['area']);
            $ams = trim($row['ams']);

            $area = Area::firstWhere('name', $area);
            $ams = AMS::firstWhere('initial', $ams);
            $customer = Customer::where('code', $customerCode)
                                ->where('name', $customerName)
                                ->first();

            $amsCustomer = AMSCustomer::where('customer_id', $customer->id)
                                    ->where('area_id', $area->id)
                                    ->where('ams_id', $ams->id)
                                    ->first();
            
            if (!$amsCustomer) {
                $amsCustomer = new AMSCustomer([
                    'customer_id' => $customer->id,
                    'area_id' => $area->id,
                    'ams_id' => $ams->id,
                ]);

                $amsCustomer->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            report($e);;
        }
    }
}
