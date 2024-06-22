<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apu;
use Illuminate\Support\Facades\Validator;

class ApuController extends Controller
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
            $paginate = Apu::all()->count();
        }

        $apu = Apu::when($search, function ($query) use ($search) {
            $query->where(function ($sub_query) use ($search) {
                $sub_query->where('name', 'LIKE', "%$search%");
            });
        })->when(($order && $by), function ($query) use ($order, $by) {
            $query->orderBy($order, $by);
        })->paginate($paginate);

        $query_string = [
            'search' => $search,
            'order' => $order,
            'by' => $by,
        ];

        $apu->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $apu
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:apu_id|max:255',
        ]);

        $apu = Apu::create($request->all());

        return response()->json([
            'message' => 'Apu has been created successfully!',
            'data' => $apu,
        ], 201);
    }

    public function show($id)
    {
        if ($apu = Apu::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $apu
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($apu = Apu::find($id)) {
            $request->validate([
                'name' => 'required|unique:apu_id,name,' . $id . '|max:255',
            ]);

            $apu->update($request->all());

            return response()->json([
                'message' => 'Apu has been updated successfully!',
                'data' => $apu,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($apu = Apu::find($id)) {
            $apu->delete();
            return response()->json([
                'message' => 'Apu has been deleted successfully!',
                'data'    => $apu
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
