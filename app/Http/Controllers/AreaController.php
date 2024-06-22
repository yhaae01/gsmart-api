<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
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
            $paginate = Area::all()->count();
        }

        $area = Area::when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('name', 'LIKE', "%$search%")
                    ->orWhere('scope', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
        ];

        $area->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $area,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:areas|max:255',
            'scope' => 'required|max:255',
        ]);

        $area = Area::create($request->all());

        return response()->json([
            'message' => 'Area has been created successfully!',
            'data' => $area,
        ], 201);
    }

    public function show($id)
    {
        if ($area = Area::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $area
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($area = Area::find($id)) {
            $request->validate([
                'name'  => 'required|unique:areas,name,' . $id . '|max:250',
                'scope' => 'required|max:255',
            ]);

            $area->update($request->all());

            return response()->json([
                'message' => 'Area has been updated successfully!',
                'data' => $area,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($area = Area::find($id)) {
            $area->delete();
            return response()->json([
                'message' => 'Area has been deleted successfully!',
                'data'    => $area
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
