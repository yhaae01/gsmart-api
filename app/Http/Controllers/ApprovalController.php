<?php

namespace App\Http\Controllers;

use App\Models\Approval;
use Illuminate\Http\Request;

class ApprovalController extends Controller
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

        $approval = Approval::with('sales_requirement_id', 'user_id')->when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('sales_requirment_id', 'LIKE', "%$search%")
                    ->orWhere('user_id', 'LIKE', "%$search%")
                    ->orWhere('sequence', 'LIKE', "%$search%")
                    ->orWhere('status', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order'  => $order,
            'by'     => $by,
        ];

        $approval->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data'    => $approval,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'sales_requirment_id' => 'required|numeric',
            'user_id'             => 'required|numeric',
            'status'              => 'required|numeric',
            'sequence'            => 'required|numeric',
        ]);

        $approval = Approval::create($request->all());

        return response()->json([
            'message' => 'Approval has been created successfully!',
            'data'    => $approval,
        ], 201);
    }

    public function show($id)
    {
        if ($approval = Approval::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data'    => $approval
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($approval = Approval::find($id)) {
            $request->validate([
                'sales_requirment_id' => 'required|unique:approvals,sales_requirment_id,' . $id . '',
                'user_id'             => 'required|numeric',
                'status'              => 'required|numeric',
                'sequence'            => 'required|numeric',
            ]);

            $approval->update($request->all());

            return response()->json([
                'message' => 'Approval has been updated successfully!',
                'data'    => $approval,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($approval = Approval::find($id)) {
            $approval->delete();
            return response()->json([
                'message' => 'Approval has been deleted successfully!',
                'data'    => $approval
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
