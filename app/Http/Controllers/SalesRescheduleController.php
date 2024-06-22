<?php

namespace App\Http\Controllers;

use App\Models\SalesReschedule;
use Illuminate\Http\Request;

class SalesRescheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        if ($request->get('order') && $request->get('by')) {
            $order = $request->get('order');
            $by = $request->get('by');
        } else {
            $order = 'id';
            $by = 'desc';
        }

        if ($request->get('paginate')) {
            $paginate = $request->get('paginate');
        } else {
            $paginate = 10;
        }
        
        $sales_reschedule = SalesReschedule::with('sales')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('sales_id', 'LIKE', "%$search%")
                    ->orWhere('start_date', 'LIKE', "%$search%")
                    ->orWhere('end_date', 'LIKE', "%$search%")
                    ->orWhere('tat', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order'  => $order,
            'by'     => $by,
        ];

        $sales_reschedule->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data'    => $sales_reschedule,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'sales_id'     => 'required|numeric',
            'start_date'   => 'required',
            'end_date'     => 'required',
            'tat'          => 'required|numeric',
            'registration' => 'required',
        ]);

        $sales_reschedule = SalesReschedule::create($request->all());

        return response()->json([
            'message' => 'Data has been created successfully!',
            'data'    => $sales_reschedule,
        ], 201);
    }

    public function show($id)
    {
        if ($sales_reschedule = SalesReschedule::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data'    => $sales_reschedule
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($sales_reschedule = SalesReschedule::find($id)) {
            $request->validate([
                'sales_id'     => 'required|unique:sales_reschedules,sales_id,' . $id . '',
                'start_date'   => 'required',
                'end_date'     => 'required',
                'tat'          => 'required|numeric',
                'registration' => 'required',
            ]);

            $sales_reschedule->update($request->all());

            return response()->json([
                'message' => 'Data has been updated successfully!',
                'data'    => $sales_reschedule,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($sales_reschedule = SalesReschedule::find($id)) {
            $sales_reschedule->delete();
            return response()->json([
                'message' => 'Data has been deleted successfully!',
                'data'    => $sales_reschedule
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
