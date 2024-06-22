<?php

namespace App\Http\Controllers;

use App\Models\SalesUpdate;
use Illuminate\Http\Request;

class SalesUpdateController extends Controller
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

        $sales_update = SalesUpdate::with('sales')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('sales_id', 'LIKE', "%$search%")
                    ->orWhere('detail', 'LIKE', "%$search%")
                    ->orWhere('reason', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order'  => $order,
            'by'     => $by,
        ];

        $sales_update->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data'    => $sales_update,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'sales_id' => 'required|numeric',
            'detail'   => 'required',
            'reason'   => 'required',
        ]);

        $sales_update = SalesUpdate::create($request->all());

        return response()->json([
            'message' => 'Data has been created successfully!',
            'data'    => $sales_update,
        ], 201);
    }

    public function show($id)
    {
        if ($sales_update = SalesUpdate::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data'    => $sales_update
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($sales_update = SalesUpdate::find($id)) {
            $request->validate([
                'sales_id' => 'required|unique:sales_updates,sales_id,' . $id . '',
                'detail'   => 'required',
                'reason'   => 'required',
            ]);

            $sales_update->update($request->all());

            return response()->json([
                'message' => 'Data has been updated successfully!',
                'data'    => $sales_update,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($sales_update = SalesUpdate::find($id)) {
            $sales_update->delete();
            return response()->json([
                'message' => 'Data has been deleted successfully!',
                'data'    => $sales_update
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
