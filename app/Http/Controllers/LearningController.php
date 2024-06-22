<?php

namespace App\Http\Controllers;

use App\Models\Learning;
use Illuminate\Http\Request;

class LearningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $learnings = Learning::get();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $learnings,
        ], 200);
    }
}
