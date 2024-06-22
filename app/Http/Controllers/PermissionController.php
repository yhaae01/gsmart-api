<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;


class PermissionController extends Controller
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
            $paginate = Permission::all()->count();
        }

        $permission = Permission::when($search, function ($query) use ($search) {
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

        $permission->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $permission,
        ], 200);
    }

    public function show($id)
    {
        if ($permission = Permission::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $permission
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($permission = Permission::find($id)) {
            $request->validate([
                'description' => 'required|max:255|unique:permissions,description,' . $id . '',
            ]);

            $permission->update($request->all());

            return response()->json([
                'message' => 'Permission has been updated successfully!',
                'data' => $permission,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
