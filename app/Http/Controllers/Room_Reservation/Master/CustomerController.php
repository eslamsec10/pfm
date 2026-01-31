<?php

namespace App\Http\Controllers\Room_Reservation\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->bulk_ids;

        $search      = $request['search'];
        $query_param = $search ? ['search' => $request['search']] : '';
        $customer   = Customer::when($request['search'], function ($q) use ($request) {
            $key = explode(' ', $request['search']);
            foreach ($key as $value) {
                $q->Where('name', 'like', "%{$value}%")
                    ->orWhere('id', $value);
            }
        })
            ->latest()->paginate()->appends($query_param);

        $data = [
            'main'   => $customer,
            'search' => $search,
            'route'  => 'customer',

        ];
        return view("admin-views.room_reservation.customer.index", $data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'gender'        => 'nullable|in:male,female',
            'birthdate'     => 'nullable',
            'id_type'   => 'nullable|in:passport,id_card,driving_license',
            'document_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        $customer = new Customer();
        if ($request->birthdate) {
            $birthdate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->birthdate);
            $customer->birthdate = $birthdate->format('Y-m-d');

            $customer->age = $birthdate->age;
        } else {
            $customer->birthdate = null;
            $customer->age = null;
        }

        $customer->name       = $request->name;
        $customer->gender     = $request->gender;
        $customer->birthdate  = $request->birthdate ? \Carbon\Carbon::createFromFormat('d/m/Y', $request->birthdate)->format('Y-m-d') : null;
        $customer->id_type    = $request->id_type ?? null;

        if ($request->hasFile('document_file')) {
            $file     = $request->file('document_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/customers', $filename, 'public');
            $customer->document_file = $filename;
        }

        $customer->save();

        return redirect()->back()->with('success', 'Customer saved successfully!')->withInput();
    }

    public function update(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:customers,id',
            'name'          => 'required|string|max:255',
            'gender'        => 'nullable|in:male,female',
            'birthdate'     => 'nullable',
            'id_type'   => 'nullable|in:passport,id_card,driving_license',
            'document_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $customer = Customer::findOrFail($request->id);
        $customer->name    = $request->name;
        $customer->gender  = $request->gender;
        $customer->id_type = $request->id_type;

        if ($request->birthdate) {
            $birthdate = \Carbon\Carbon::createFromFormat('d/m/Y', $request->birthdate);
            $customer->birthdate = $birthdate->format('Y-m-d');
            $customer->age = $birthdate->age;
        }

        if ($request->hasFile('document_file')) {
            $file     = $request->file('document_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/customers', $filename, 'public');
            $customer->document_file = $filename;
        }

        $customer->save();

        return redirect()->back()->with('success', 'Customer updated successfully!');
    }


    public function delete(Request $request)
    {
        $customer = Customer::findOrFail($request->id);
        if ($customer->delete()) {
            return redirect()->route("customer.list")->with("success", ui_change('deleted_successfully'));
        }
        return redirect()->back()->with('error', ui_change('error_in_deleted'));
    }

    public function edit($id)
    {
        $main_info = Customer::findOrFail($id);
        if ($main_info) {
            return response()->json([
                'status'    => 200,
                "main_info" => $main_info,
            ]);
        } else {
            return response()->json([
                'status'  => 404,
                "message" => "Receipt Settings Not Found",
            ]);
        }
    }
}
