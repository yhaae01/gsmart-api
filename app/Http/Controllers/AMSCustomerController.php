<?php

namespace App\Http\Controllers;

use App\Models\AMSCustomer;
use Illuminate\Http\Request;

class AMSCustomerController extends Controller
{
    public function __invoke($id)
    {
        $ams_customer = AMSCustomer::where('customer_id', $id)->get();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $ams_customer,
        ], 200);
    }
}
