<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Models\User;
use App\Models\Address;
use App\Models\CMS\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\AddressStoreRequest;
use App\Models\CMS\Area;
use App\Models\CMS\City;

class CustomerAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $customerId)
    {
        $customer = User::findOrFail($customerId);

        $data['customer'] = $customer;
        $data['addressTypes']   = Address::getTypes();
        $data['provinces']      = Province::where('country_id', 1)->get();

        $response['view'] = view('theme.adminlte.sales.customers.addresses.create', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddressStoreRequest $request, string $customerId)
    {
        $customer = User::findOrFail($customerId);

        $validated = $request->validated();

        $address = $customer->addresses()->create([
            'phone' => $validated['phone'],
            'type' => $validated['type'],
            'country_id' => 1, // UAE
            'province_id' => $validated['province_id'],
            'city_id' => $validated['city_id'],
            'area_id' => $validated['area_id'],
            'address' => $validated['address'],
            'landmark' => $validated['landmark'] ?? null,
        ]);

        if ($customer->addresses()->count() === 1) {
            $customer->update(['default_address_id' => $address->id]);
        }

        return response()->json([
            'success' => true,
            'message' => __('crud.created', ['name' => 'Customer Address']),
            'redirect' => route('admin.sales.customers.show', ['customer' => $customer])
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $customerId, string $id)
    {
        $customer = User::findOrFail($customerId);
        $address = Address::where('user_id', $customerId)->findOrFail($id);
        $data['addressTypes']   = Address::getTypes();
        $data['provinces']      = Province::where('country_id', 1)->get();
        $data['cities']      = City::where('province_id', $address->province_id)->get();
        $data['areas']      = Area::where('city_id', $address->city_id)->get();

        $data['customer'] = $customer;
        $data['address'] = $address;

        $response['view'] = view('theme.adminlte.sales.customers.addresses.edit', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
