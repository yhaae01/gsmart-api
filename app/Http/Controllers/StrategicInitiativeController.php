<?php

namespace App\Http\Controllers;

use App\Models\StrategicInitiatives;
use Illuminate\Http\Request;

class StrategicInitiativeController extends Controller
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
            $paginate = StrategicInitiatives::all()->count();
        }

        $strategic_initiative = StrategicInitiatives::when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('name', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
        ];

        $strategic_initiative->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $strategic_initiative,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:strategic_initiatives|max:255',
            'description' => 'required|max:255',
        ]);

        $strategic_initiative = StrategicInitiatives::create($request->all());

        return response()->json([
            'message' => 'Strategic Initiative has been created successfully!',
            'data' => $strategic_initiative,
        ], 201);
    }

    public function show($id)
    {
        if ($strategic_initiative = StrategicInitiatives::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $strategic_initiative
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($strategic_initiative = StrategicInitiatives::find($id)) {
            $request->validate([
                'name' => 'required|unique:strategic_initiatives,name,' . $id . '|max:255',
                'description' => 'required|max:255',
            ]);

            $strategic_initiative->update($request->all());

            return response()->json([
                'message' => 'Strategic Initiative has been updated successfully!',
                'data' => $strategic_initiative,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($strategic_initiative = StrategicInitiatives::find($id)) {
            $strategic_initiative->delete();
            return response()->json([
                'message' => 'Strategic Initiative has been deleted successfully!',
                'data'    => $strategic_initiative
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
