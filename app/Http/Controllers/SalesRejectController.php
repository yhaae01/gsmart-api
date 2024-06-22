<?php

namespace App\Http\Controllers;

use App\Models\SalesReject;
use Illuminate\Http\Request;

class SalesRejectController extends Controller
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

        $sales_reject = SalesReject::with('sales_id')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('sales_id', 'LIKE', "%$search%")
                    ->orWhere('category', 'LIKE', "%$search%")
                    ->orWhere('reason', 'LIKE', "%$search%")
                    ->orWhere('competitor', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
        ];

        $sales_reject->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $sales_reject,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'sales_id'   => 'required|numeric',
            'category'   => 'required',
            'reason'     => 'required',
        ]);

        $sales_reject = SalesReject::create($request->all());

        return response()->json([
            'message' => 'Data has been created successfully!',
            'data'    => $sales_reject,
        ], 201);
    }

    public function show($id)
    {
        if ($sales_reject = SalesReject::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $sales_reject
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($sales_reject = SalesReject::find($id)) {
            $request->validate([
                'sales_id'   => 'required|unique:sales_rejects,sales_id,' . $id . '',
                'category'   => 'required',
                'reason'     => 'required',
            ]);

            $sales_reject->update($request->all());

            return response()->json([
                'message' => 'Data has been updated successfully!',
                'data' => $sales_reject,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($sales_reject = SalesReject::find($id)) {
            $sales_reject->delete();
            return response()->json([
                'message' => 'Data has been deleted successfully!',
                'data'    => $sales_reject
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
