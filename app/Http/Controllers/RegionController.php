<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
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
            $paginate = Region::all()->count();
        }

        $region = Region::with('countries')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('name', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
        ];

        $region->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $region,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:regions|max:255',
        ]);

        $region = Region::create($request->all());

        return response()->json([
            'message' => 'Region has been created successfully!',
            'data' => $region,
        ], 201);
    }

    public function show($id)
    {
        if ($region = Region::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $region
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($region = Region::find($id)) {
            $request->validate([
                'name'    => 'required|unique:regions,name,' . $id . '|max:255',
            ]);

            $region->update($request->all());

            return response()->json([
                'message' => 'Region has been updated successfully!',
                'data' => $region,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($region = Region::find($id)) {
            $region->delete();
            return response()->json([
                'message' => 'Region has been deleted successfully!',
                'data'    => $region
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
