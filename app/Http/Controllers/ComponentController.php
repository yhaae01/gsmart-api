<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Component;

class ComponentController extends Controller
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
            $paginate = Component::all()->count();
        }

        $component = Component::when($search, function ($query) use ($search) {
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

        $component->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $component
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:component_id|max:255',
        ]);

        $component = Component::create($request->all());

        return response()->json([
            'message' => 'Component has been created successfully!',
            'data' => $component,
        ], 201);
    }

    public function show($id)
    {
        if ($component = Component::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $component
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($component = Component::find($id)) {
            $request->validate([
                'name' => 'required|unique:component_id,name,' . $id . '|max:255',
            ]);

            $component->update($request->all());

            return response()->json([
                'message' => 'Component has been updated successfully!',
                'data' => $component,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($component = Component::find($id)) {
            $component->delete();
            return response()->json([
                'message' => 'Component has been deleted successfully!',
                'data'    => $component
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
