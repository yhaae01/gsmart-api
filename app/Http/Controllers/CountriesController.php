<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use Illuminate\Http\Request;

class CountriesController extends Controller
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
            $paginate = Countries::all()->count();
        }

        $countries = Countries::with('region')
                            ->search($search)
                            ->sort($order, $by)
                            ->paginate($paginate)
                            ->withQueryString();

        return response()->json([
            'message' => 'Success!',
            'data' => $countries,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:countries|max:255',
            'region_id' => 'required',
        ]);

        $countries = Countries::create($request->all());

        return response()->json([
            'message' => 'Countries has been created successfully!',
            'data' => $countries,
        ], 201);
    }

    public function show($id)
    {
        if ($countries = Countries::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $countries
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($countries = Countries::find($id)) {
            $request->validate([
                'name'  => 'required|unique:countries,name,' . $id . '|max:255',
                'region_id' => 'required',
            ]);

            $countries->update($request->all());

            return response()->json([
                'message' => 'Countries has been updated successfully!',
                'data' => $countries,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($countries = Countries::find($id)) {
            $countries->delete();
            return response()->json([
                'message' => 'Countries has been deleted successfully!',
                'data'    => $countries
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
