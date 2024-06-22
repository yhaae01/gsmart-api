<?php

namespace App\Http\Controllers;

use App\Models\Hangar;
use Illuminate\Http\Request;

class HangarController extends Controller
{
    public function __invoke()
    {
        $hangars = Hangar::all();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $hangars,
        ], 200);
    }
}
