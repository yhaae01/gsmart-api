<?php

namespace App\Http\Controllers;

use App\Models\SalesRequirement;
use Illuminate\Http\Request;

class SalesRequirementController extends Controller
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

        $sales_requirement = SalesRequirement::with('sales_id', 'requirement_id')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('sales_id', 'LIKE', "%$search%")
                    ->orWhere('requirement_id', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
        ];

        $sales_requirement->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $sales_requirement,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'sales_id'       => 'required|numeric',
            'requirement_id' => 'required|numeric',
            'status'         => 'required',
        ]);

        $sales_requirement = SalesRequirement::create($request->all());

        return response()->json([
            'message' => 'Data has been created successfully!',
            'data'    => $sales_requirement,
        ], 201);
    }

    public function show($id)
    {
        if ($sales_requirement = SalesRequirement::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $sales_requirement
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($sales_requirement = SalesRequirement::find($id)) {
            $request->validate([
                'sales_id'       => 'required|unique:sales_requirements,sales_id,' . $id . '',
                'requirement_id' => 'required|unique:sales_requirements,requirement_id,' . $id . '',
                'detail'         => 'required',
            ]);

            $sales_requirement->update($request->all());

            return response()->json([
                'message' => 'Data has been updated successfully!',
                'data' => $sales_requirement,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($sales_requirement = SalesRequirement::find($id)) {
            $sales_requirement->delete();
            return response()->json([
                'message' => 'Data has been deleted successfully!',
                'data'    => $sales_requirement
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
