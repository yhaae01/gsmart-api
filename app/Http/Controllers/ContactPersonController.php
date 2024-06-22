<?php

namespace App\Http\Controllers;

use App\Models\ContactPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ContactPersonController extends Controller
{
    public function index(Request $request)
    {
        $customer_id = $request->customer ?? false;

        $contact_persons = ContactPerson::byCustomer($customer_id)
                                        ->latest()
                                        ->paginate(10)
                                        ->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Retrieve data successfully',
            'data' => $contact_persons,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|string|email|unique:contact_persons,email',
            'address' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'customer_id' => 'required|integer',
        ]);

        $customer_cp = new ContactPerson;
        $customer_cp->name = $request->name;
        $customer_cp->phone = $request->phone;
        $customer_cp->email = $request->email;
        $customer_cp->address = $request->address;
        $customer_cp->customer_id = $request->customer_id;
        $customer_cp->title = $request->title;
        $customer_cp->save();

        return response()->json([
            'success' => true,
            'message' => 'Contact person created successfully',
            'data' => $customer_cp,
        ], 200);
    }

    public function destroy($id)
    {
        $contact_person = ContactPerson::findOrFail($id);
        $contact_person->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact person deleted successfully',
        ], 200);
    }
}
