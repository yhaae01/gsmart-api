<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $noAdmin = $request->noAdmin ?? null;
        $search = $request->search ?? null;
        $order = $request->order ?? 'id';
        $by = $request->by ?? 'DESC';
        $paginate = $request->paginate ?? User::all()->count();

        $users = User::with('role')
                    ->when($noAdmin, function ($query) {
                        $query->where('username', '<>', 'administrator');
                    })
                    ->search($search)
                    ->sort($order, $by)
                    ->paginate($paginate)
                    ->withQueryString();

        $user_active = User::with('role.permissions.permission')->find(Auth::id());

        return response()->json([
            'message' => 'success',
            'data' => $users,
            'user' => $user_active,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name'        => 'required|string',
            'username'    => 'required|string|unique:users',
            'role_id'     => 'required|integer|exists:roles,id',
            'email'       => 'required|string|unique:users,email|email',
            'nopeg'       => 'required|integer|unique:users',
            'unit'        => 'required|string',
            'password'    => 'required|string|min:3',
        ]);

        $user = new User;
        $user->name = $request->name;
        $user->nopeg = $request->nopeg;
        $user->username = trim($request->username);
        $user->role_id = $request->role_id;
        $user->email = $request->email;
        $user->unit = strtoupper($request->unit);
        $user->password = Hash::make($request->password);
        $user->email_verified_at = Carbon::now();
        $user->save();

        $user->assignRole(User::ROLES[$user->role_id]);

        return response()->json([
            'message' => 'User created has successfully!',
            'data'    => $user,
        ], 201);
    }

    public function show($id)
    {
        if ($user = User::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($user = User::find($id)) {
            $request->validate([
                'name' => 'required|string',
                'username' => 'required|string|unique:users,username,'.$id,
                'role_id' => 'required|integer|exists:roles,id',
                'email' => 'required|string|unique:users,email,'.$id.'|email',
                'nopeg' => 'required|integer|unique:users,nopeg,'.$id,
                'unit' => 'required|string',
                'password' => 'sometimes|string|min:3',
            ]);

            $old_role = $user->role_id;

            $user->name = $request->name;
            $user->username = $request->username;
            $user->role_id = $request->role_id;
            $user->email = $request->email;
            $user->nopeg = $request->nopeg;
            $user->unit = strtoupper($request->unit);
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            if ($old_role != $request->role_id) {
                $user->assignRole(User::ROLES[$user->role_id]);
            }

            return response()->json([
                'message' => 'User has been updated successfully!',
                'data' => $user,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($user = User::where('id', $id)->first()) {
            $user->delete();
            return response()->json([
                'message' => 'User has been deleted successfully!',
                'data'    => $user
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
