<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HRM\Employee;

class AuthController extends Controller
{
    public function username()
    {
        return 'username';
    }

    public function credentials(Request $request)
    {
        return [
            'samaccountname' => $request->username,
            'password' => $request->password,
            'fallback' => [
                'username' => $request->username,
                'password' => $request->password,
            ],
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($this->credentials($request))) {
            $user = Auth::user();

            if (!$user->unit) {
                $this->setUnit($user);
            }

            if (!$user->role_id) {
                $this->setRole($user);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully',
                'token' => $user->createToken('token')->plainTextToken,
                'user' => $user,
            ], 200);

            $request->session()->regenerate();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'These credentials not match our records',
            ], 401);
        }
    }

    private function setUnit(User $user)
    {
        $empl = Employee::where('PERNR', $user->username)
                        ->orWhere('EMAIL', $user->email)
                        ->first();

        $user->unit = $empl->UNIT;
        $user->push();
    }

    private function setRole(User $user)
    {
        $empl = Employee::where('PERNR', $user->username)
                        ->orWhere('EMAIL', $user->email)
                        ->first();

        $unit = $empl->UNIT;
        $role = $empl->JABATAN;

        if ($unit == 'TPR') {
            $user->role_id = User::ROLE_TPR;
        } else if (in_array($unit, ['TPW','TPY','TPX'])) {
            if (str_contains($unit, 'Area Manager')) {
                $user->role_id = User::ROLE_AM;
            } else {
                $user->role_id = User::ROLE_AMS;
            }
        } else if ($unit == 'TPC') {
            $user->role_id = User::ROLE_TPC;
        } else {
            $user->role_id = User::ROLE_INIT;
        }

        $user->push();
        $user->assignRole(User::ROLES[$user->role_id]);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->user()->tokens()->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully!',
        ], 200);
    }

    public function me(Request $request)
    {
        $user = User::with('role.permissions.permission')
                    ->where('id', Auth::id())
                    ->first();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $user,
        ], 200);
    }
}
