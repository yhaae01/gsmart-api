<?php

namespace App\Http\Controllers;

use App\Models\Level;
use Illuminate\Http\Request;

class LevelController extends Controller
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

        $level = Level::with('requirements')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('level', 'LIKE', "%$search%")
                    ->orWhere('description', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order'  => $order,
            'by'     => $by,
        ];

        $level->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data'    => $level,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'level'       => 'required|numeric|unique:levels,level',
            'description' => 'required',
        ]);

        $level = Level::create($request->all());

        return response()->json([
            'message' => 'Level has been created successfully!',
            'data'    => $level,
        ], 201);
    }

    public function show($id)
    {
        if ($level = Level::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data'    => $level
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($level = Level::find($id)) {
            $request->validate([
                'level'       => 'required|unique:levels,level,' . $id . '',
                'description' => 'required',
            ]);

            $level->update($request->all());

            return response()->json([
                'message' => 'Level has been updated successfully!',
                'data'    => $level,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($level = Level::find($id)) {
            $level->delete();
            return response()->json([
                'message' => 'Level has been deleted successfully!',
                'data'    => $level
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
