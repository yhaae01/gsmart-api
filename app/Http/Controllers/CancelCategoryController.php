<?php

namespace App\Http\Controllers;

use App\Models\CancelCategory;
use Illuminate\Http\Request;

class CancelCategoryController extends Controller
{
    public function __invoke(Request $request)
    {
        $categories = CancelCategory::all();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $categories,
        ], 200);
    }
}
