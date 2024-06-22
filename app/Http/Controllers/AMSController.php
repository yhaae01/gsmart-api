<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\AMS;
use App\Models\AMSCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class AMSController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? null;
        $order = $request->order ?? 'initial';
        $by = $request->by ?? 'ASC';
        $paginate = $request->paginate ?? 10;

        $ams = AMS::with(['user', 'am', 'amsCustomers.area', 'amsCustomers.customer'])
                ->whereHas('user')
                ->when((auth()->user()->role->name == 'AM'), function ($query) {
                    $query->where('am_id', auth()->user()->id);
                })
                ->search($search)
                ->sort($order, $by)
                ->paginate($paginate)
                ->withQueryString();

        return response()->json([
            'message' => 'Success!',
            'data' => $ams,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'initial' => 'required|string|unique:ams,initial',
            'user_id' => 'required|integer|exists:users,id',
            'am_id' => 'sometimes|required|integer|exists:am,id',
            'area_customer' => 'sometimes|required|array',
            'area_customer.*.area_id' => 'sometimes|required|integer|exists:areas,id',
            'area_customer.*.customer_id' => 'sometimes|required|integer|exists:customers,id',
        ]);

        try {
            DB::beginTransaction();

            $ams = new AMS;
            $ams->initial = trim($request->initial);
            $ams->user_id = $request->user_id;
            $ams->am_id = $request->am_id ?? null;
            $ams->save();

            if ($request->area_customer) {
                foreach ($request->area_customer as $area_customer) {
                    $ams_customer = new AMSCustomer;
                    $ams_customer->customer_id = $area_customer['customer_id'];
                    $ams_customer->area_id = $area_customer['area_id'];
                    $ams_customer->ams_id = $ams->id;
                    $ams_customer->save();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'AMS has been created successfully!',
                'data' => $ams->load(['user', 'am'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to create AMS',
            ], 500);
        }
    }

    public function show($id)
    {
        $ams = AMS::with(['amsCustomers.area', 'amsCustomers.customer', 'sales'])
                    ->find($id);

        if ($ams) {
            return response()->json([
                'message' => 'Success!',
                'data' => $ams
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $ams = AMS::findOrFail($id);

        $request->validate([
            'initial' => 'required|string|unique:ams,initial,'.$id,
            'user_id' => 'required|integer|exists:users,id',
            'am_id' => 'sometimes|required|integer|exists:am,id',
            'area_customer' => 'sometimes|required|array',
            'area_customer.*.area_id' => 'sometimes|required|integer|exists:areas,id',
            'area_customer.*.customer_id' => 'sometimes|required|integer|exists:customers,id',
        ]);

        try {
            DB::beginTransaction();

            $ams->initial = trim($request->initial);
            $ams->user_id = $request->user_id;
            $ams->am_id = $request->am_id ?? null;
            $ams->initial = $request->initial;
            $ams->push();

            if ($request->area_customer) {
                $old_items = AMSCustomer::where('ams_id', $ams->id)
                                        ->get(['area_id', 'customer_id'])
                                        ->makeHidden('label');

                $exists_items = new Collection();

                foreach ($request->area_customer as $area_customer) {
                    $exists = $old_items->where('customer_id', $area_customer['customer_id'])
                                        ->where('area_id', $area_customer['area_id'])
                                        ->first();

                    if (!$exists) {
                        $ams_customer = new AMSCustomer;
                        $ams_customer->customer_id = $area_customer['customer_id'];
                        $ams_customer->area_id = $area_customer['area_id'];
                        $ams_customer->ams_id = $ams->id;
                        $ams_customer->save();
                    }else {
                        $exists_items->push((object)$exists);
                    }
                }

                $deleted_items = $old_items->diffAssoc($exists_items)->values();

                foreach ($deleted_items as $item) {
                    AMSCustomer::where('ams_id', $ams->id)
                                ->where('area_id', $item->area_id)
                                ->where('customer_id', $item->customer_id)
                                ->delete();
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'AMS has been updated successfully!',
                'data' => $ams,
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error occured, failed to update AMS data',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $ams = AMS::findOrFail($id);

        $ams->delete();

        return response()->json([
            'message' => 'AMS has been deleted successfully!',
            'data'    => $ams
        ], 200);
    }
}
