<?php

namespace App\Http\Controllers;

use App\Models\SalesLevel;
use Illuminate\Http\Request;

class SalesLevelController extends Controller
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

        $sales_level = SalesLevel::with('sales', 'level')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('sales_id', 'LIKE', "%$search%")
                    ->orWhere('level_id', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order'  => $order,
            'by'     => $by,
        ];

        $sales_level->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data'    => $sales_level,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'sales_id'  => 'required|numeric',
            'level_id'  => 'required|numeric',
            'status_id' => 'required|numeric',
        ]);

        $sales_level = SalesLevel::create($request->all());

        return response()->json([
            'message' => 'Data has been created successfully!',
            'data'    => $sales_level,
        ], 201);
    }

    public function show($id)
    {
        if ($sales_level = SalesLevel::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data'    => $sales_level
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($sales_level = SalesLevel::find($id)) {
            $request->validate([
                'sales_id'  => 'required|unique:sales_levels,sales_id,' . $id . '',
                'level_id'  => 'required|unique:sales_levels,level_id,' . $id . '',
                'status_id' => 'required|unique:sales_levels,status_id,' . $id . '',
            ]);

            $sales_level->update($request->all());

            return response()->json([
                'message' => 'Data has been updated successfully!',
                'data'    => $sales_level,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($sales_level = SalesLevel::find($id)) {
            $sales_level->delete();
            return response()->json([
                'message' => 'Data has been deleted successfully!',
                'data'    => $sales_level
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
