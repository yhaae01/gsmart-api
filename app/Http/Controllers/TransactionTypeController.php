<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransactionType;

class TransactionTypeController extends Controller
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
            $paginate = TransactionType::all()->count();
        }

        $transaction_type = TransactionType::when($search, function ($query) use ($search) {
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

        $transaction_type->appends($query_string);

        return response()->json([
            'message' => 'Success!',
            'data' => $transaction_type,
        ], 200);
    }

    public function show($id)
    {
        if ($transaction_type = TransactionType::find($id)) {
            return response()->json([
                'message' => 'Success!',
                'data' => $transaction_type
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        if ($transaction_type = TransactionType::find($id)) {
            $request->validate([
                'description' => 'required|max:255',
            ]);

            $transaction_type->update($request->all());

            return response()->json([
                'message' => 'Transaction Type has been updated successfully!',
                'data' => $transaction_type,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Data not found!',
            ], 404);
        }
    }
}
