<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AircraftType;
use Illuminate\Support\Facades\Validator;

class AircraftTypeController extends Controller
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
            $paginate = AircraftType::all()->count();
        }

        $ac_type_id = AircraftType::when($search, function ($query) use ($search) {
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

        $ac_type_id->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $ac_type_id
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:ac_type_id|max:255',
        ]);

        $ac_type_id = AircraftType::create($request->all());

        return response()->json([
            'message' => 'Aircraft Type has been created successfully!',
            'data' => $ac_type_id,
        ], 201);
    }

    public function show($id)
    {
        if ($ac_type_id = AircraftType::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $ac_type_id
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($ac_type_id = AircraftType::find($id)) {
            $request->validate([
                'name' => 'required|unique:ac_type_id,name,' . $id . '|max:255',
            ]);

            $ac_type_id->update($request->all());

            return response()->json([
                'message' => 'Aircraft Type has been updated successfully!',
                'data' => $ac_type_id,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($ac_type_id = AircraftType::find($id)) {
            $ac_type_id->delete();
            return response()->json([
                'message' => 'Aircraft Type has been deleted successfully!',
                'data'    => $ac_type_id
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
