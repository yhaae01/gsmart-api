<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
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
            $paginate = Product::all()->count();
        }

        $product = Product::when($search, function ($query) use ($search) {
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

        $product->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $product,
        ], 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:products|max:255',
            'description' => 'required|max:255',
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Product has been created successfully!',
            'data' => $product,
        ], 201);
    }

    public function show($id)
    {
        if ($product = Product::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $product
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($product = Product::find($id)) {
            $request->validate([
                'name' => 'required|unique:products,name,' . $id . '|max:255',
                'description' => 'required|max:255',
            ]);

            $product->update($request->all());

            return response()->json([
                'message' => 'Product has been updated successfully!',
                'data' => $product,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function destroy($id)
    {
        if ($product = Product::find($id)) {
            $product->delete();

            return response()->json([
                'message' => 'Product has been deleted successfully!',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
