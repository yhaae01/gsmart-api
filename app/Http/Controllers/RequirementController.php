<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;

class RequirementController extends Controller
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
            $paginate = Requirement::all()->count();
        }

        $requirement = Requirement::with('level')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('level_id', 'LIKE', "%$search%")
                    ->orWhere('requirement', $search);
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order'  => $order,
            'by'     => $by,
        ];

        $requirement->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data'    => $requirement,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'level_id'    => 'required|numeric',
            'requirement' => 'required',
        ]);

        $requirement = Requirement::create($request->all());

        return response()->json([
            'message' => 'Requirement has been created successfully!',
            'data'    => $requirement,
        ], 201);
    }

    public function show($id)
    {
        if ($requirement = Requirement::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data'    => $requirement
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($requirement = Requirement::find($id)) {
            $request->validate([
                'level_id'    => 'required|unique:requirements,level_id,' . $id . '',
                'requirement' => 'required',
            ]);

            $requirement->update($request->all());

            return response()->json([
                'message' => 'Requirement has been updated successfully!',
                'data'    => $requirement,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($requirement = Requirement::find($id)) {
            $requirement->delete();
            return response()->json([
                'message' => 'Requirement has been deleted successfully!',
                'data'    => $requirement
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
