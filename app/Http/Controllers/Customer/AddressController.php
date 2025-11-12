<?php

namespace App\Http\Controllers\Customer;

use App\Models\User;
use App\Models\Address;
use App\Models\CMS\Area;
use App\Models\CMS\City;
use App\Models\CMS\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Sales\CustomerAddress;
use App\Http\Requests\Sales\AddressStoreRequest;
use App\Http\Requests\Sales\AddressUpdateRequest;

class AddressController extends Controller
{
    /**
     * Show address list.
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->latest()->get();
        return view('theme.meals.customer.addresses.index', compact('addresses'));
    }

    /**
     * Load the Add Address form HTML (for modal).
     */
    public function create()
    {
        $customer = Auth::user();
        $data['customer'] = $customer;
        $data['addressTypes'] = Address::getTypes();
        $data['provinces'] = Province::where('country_id', 1)->get();

        $html = view('theme.meals.customer.addresses.create', $data)->render();

        return response()->json([
            'success' => true,
            'view' => $html,
        ]);
    }

    /**
     * Save new address.
     */
    public function store(AddressStoreRequest $request)
    {
        $customer = Auth::user();

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
            'message' => 'Address added successfully!',
            'redirect' => route('customer.addresses.index'),
        ]);
    }

    public function edit(string $id)
    {
        $customer = Auth::user();
        $address = Address::where('user_id', $customer->id)->findOrFail($id);
        $data['addressTypes']   = Address::getTypes();
        $data['provinces']      = Province::where('country_id', 1)->get();
        $data['cities']         = City::where('province_id', $address->province_id)->get();
        $data['areas']          = Area::where('city_id', $address->city_id)->get();

        $data['customer'] = $customer;
        $data['address'] = $address;


        $html = view('theme.meals.customer.addresses.edit', $data)->render();

        return response()->json([
            'success' => true,
            'view' => $html,
        ]);
    }

    public function update(AddressUpdateRequest $request, string $id)
    {
        $validated = $request->validated();

        $customer = Auth::user();
        $address  = Address::where('user_id', $customer->id)->findOrFail($id);

        $hasSubscription = $address->subscriptions()->exists();
        if ($hasSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'This address is linked to a subscription and cannot be modified.',
            ], 403);
        }


        $address->update([
            'phone'       => $validated['phone'],
            'type'        => $validated['type'],
            'province_id' => $validated['province_id'],
            'city_id'     => $validated['city_id'],
            'area_id'     => $validated['area_id'],
            'address'     => $validated['address'],
            'landmark'    => $validated['landmark'] ?? null,
        ]);

        // Update customer phone if empty
        if (empty($customer->phone)) {
            $customer->update(['phone' => $validated['phone']]);
        }

        return response()->json([
            'success'  => true,
            'message'  => __('crud.updated', ['name' => 'Customer Address']),
            'redirect' => route('customer.addresses.index'),
        ]);
    }

    public function destroy(string $id)
    {
        $customer = Auth::user();
        $address  = Address::where('user_id', $customer->id)->findOrFail($id);

        $hasSubscription = $address->subscriptions()->exists();
        if ($hasSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'This address cannot be deleted because it is linked to an active or past subscription.',
            ], 403);
        }

        $address->delete();

        if ($customer->default_address_id === $address->id) {
            $customer->update(['default_address_id' => null]);
        }

        return response()->json([
            'success'  => true,
            'message'  => __('crud.deleted', ['name' => 'Customer Address']),
            'redirect' => route('customer.addresses.index'),
        ]);
    }
}
