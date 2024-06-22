<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AM;

class AMController extends Controller
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
            $paginate = AM::all()->count();
        }

        $am = AM::with('user')
                ->whereHas('user')
                ->with('area')
                ->whereHas('area')
                ->search($search)
                ->sort($order, $by)
                ->paginate($paginate)
                ->withQueryString();

        return response()->json([
            'message' => 'Success!',
            'data'    => $am,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'initial' => 'required|unique:am|max:255',
            'user_id' => 'required',
            'area_id' => 'required',
        ]);

        $am = AM::create($request->all());

        return response()->json([
            'message' => 'AM has been created successfully!',
            'data'    => $am,
        ], 201);
    }

    public function show($id)
    {
        if ($am = AM::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data'    => $am
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($am = AM::find($id)) {
            $request->validate([
                'initial' => 'required|unique:am,initial,' . $id . '|max:255',
                'user_id' => 'required',
                'area_id' => 'required',
            ]);

            $am->update($request->all());

            return response()->json([
                'message' => 'AM has been updated successfully!',
                'data'    => $am,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($am = AM::find($id)) {
            $am->delete();
            return response()->json([
                'message' => 'AM has been deleted successfully!',
                'data'    => $am
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
