<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerSwift;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerSwiftController extends Controller
{
    public function index(Request $request)
    {
        $order = $request->order ?? 'name';
        $by = $request->by ?? 'ASC';
        $paginate = $request->paginate ?? Customer::all()->count();

        $data = CustomerSwift::with(['customerGroup'])
                            ->orderBy($order, $by)
                            ->paginate($paginate)
                            ->withQueryString();

        return response()->json([
            'message' => 'Success!',
            'data' => $data
        ], 200);
    }

    public function show(Request $request)
    {
        $customer = CustomerSwift::with('customerGroup')
                                ->where('id', $request->id)
                                ->get();

        if ($customer->isNotEmpty()) {
            return response()->json([
                'message' => 'Success!',
                'data' => $customer->first()
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
