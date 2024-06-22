<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoleHasPermission;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
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
            $paginate = Role::all()->count();
        }

        $role = Role::with('permissions')->when($search, function ($query) use ($search) {
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

        $role->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $role,
        ], 200);
    }

    public function show($id)
    {
        if ($role = Role::with('permissions')->find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $role
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($role = Role::with('permissions')->find($id)) {
            $request->validate([
                'description' => 'required|max:255',
                'permission_id' => 'required',
            ]);

            DB::beginTransaction();
            $role->update($request->all());

            RoleHasPermission::where('role_id', $role->id)->delete();

            foreach ($request->get('permission_id') as $value) {
                RoleHasPermission::create([
                    'permission_id' => $value,
                    'role_id' => $role->id
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Role has been updated successfully!',
                'data' => $role,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
