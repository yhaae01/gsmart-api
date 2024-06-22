<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Engine;

class EngineController extends Controller
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
            $paginate = Engine::all()->count();
        }

        $engine = Engine::when($search, function ($query) use ($search) {
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

        $engine->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $engine
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:engine_id|max:255',
        ]);

        $engine = Engine::create($request->all());

        return response()->json([
            'message' => 'Engine has been created successfully!',
            'data' => $engine,
        ], 201);
    }

    public function show($id)
    {
        if ($engine = Engine::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $engine
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($engine = Engine::find($id)) {

            $request->validate([
                'name' => 'required|unique:engine_id,name,' . $id . '|max:255',
            ]);

            $engine->update($request->all());

            return response()->json([
                'message' => 'Engine has been updated successfully!',
                'data' => $engine,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($engine = Engine::find($id)) {
            $engine->delete();
            return response()->json([
                'message' => 'Engine has been deleted successfully!',
                'data'    => $engine
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
