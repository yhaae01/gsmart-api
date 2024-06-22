<?php

namespace App\Http\Controllers;

use App\Models\IGTE;
use Illuminate\Http\Request;

class IGTEController extends Controller
{
    public function index()
    {
        $igtes = IGTE::get();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $igtes,
        ], 200);
    }
}
