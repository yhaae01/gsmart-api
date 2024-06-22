<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerSwift;
use App\Models\AMSCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use App\Helpers\PaginationHelper as PG;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $hasAms = $request->hasAms ?? null;
        $search = $request->search ?? null;
        $order = $request->order ?? 'name';
        $by = $request->by ?? 'ASC';
        $paginate = $request->paginate ?? Customer::all()->count();

        $data = Customer::with(['customers', 'country.region'])
                        ->when((auth()->user()->role->name == 'AMS'), function($query) {
                            $query->whereHas('amsCustomers', function($query) {
                                $query->where('ams_id', auth()->user()->ams->id);
                            });
                        })
                        ->when($hasAms, function ($query) {
                            $query->whereHas('amsCustomers');
                        })
                        ->search($search)
                        ->orderBy($order, $by)
                        ->paginate($paginate)
                        ->withQueryString();

        return response()->json([
            'message' => 'Success!',
            'data' => $data
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'group_type' => 'required|integer',
            'country_id' => 'required|integer|exists:countries,id',
            'is_active' => 'required|boolean',
            'customer_group' => 'sometimes|required|array',
            'customer_group.*.id' => 'sometimes|required|integer|exists:customer_swift,id'
        ]);

        $customers = array_map(function($obj) {
            return $obj['id'];
        }, $request->customer_group);

        try {
            DB::beginTransaction();

            $customer = new Customer;
            $customer->code = trim($request->code);
            $customer->name = trim($request->name);
            $customer->group_type = $request->group_type;
            $customer->country_id = $request->country_id;
            $customer->is_active = $request->is_active;
            $customer->save();

            CustomerSwift::whereIn('id', $customers)
                        ->update(['customer_group_id' => $customer->id]);

            DB::commit();

            return response()->json([
                'message' => 'Customer Group has been created successfully!',
                'data' => $customer->load('customers')
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'message' => 'Error occured, no data created!',
            ], 500);
        }
    }

    public function show($id)
    {
        $data = Customer::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found!'
            ], 404);
        }

        return response()->json([
            'message' => 'Success!',
            'data' => $data->load('customers')
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string',
            'name' => 'required|string',
            'group_type' => 'required|integer',
            'country_id' => 'required|integer|exists:countries,id',
            'is_active' => 'required|boolean',
            'customer_group' => 'sometimes|required|array',
            'customer_group.*.id' => 'sometimes|required|integer|exists:customer_swift,id'
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::find($id);

            if (!$customer) throw new Exception('Data not found', 404);

            $customer->code = trim($request->code);
            $customer->name = trim($request->name);
            $customer->group_type = $request->group_type;
            $customer->country_id = $request->country_id;
            $customer->is_active = $request->is_active;
            $customer->push();

            $oldCustomers = CustomerSwift::where('customer_group_id', $customer->id)->pluck('id')->toArray();
            $newCustomers = array_map(function($obj) {
                                return $obj['id'];
                            }, $request->customer_group);
            $unassignCustomers = array_diff($oldCustomers, $newCustomers);

            CustomerSwift::whereIn('id', $newCustomers)
                        ->update(['customer_group_id' => $customer->id]);

            if (!empty($unassignCustomers)) {
                CustomerSwift::whereIn('id', $unassignCustomers)
                            ->update(['customer_group_id' => null]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Customer Group has been updated successfully!',
                'data' => $customer->load('customers')
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'message' => $e->getMessage() ?: 'Error occured, no data updated',
            ], $e->getCode() ?: 500);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'message' => 'Customer Group has been deleted successfully!'
        ], 200);
    }
}
