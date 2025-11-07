<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Models\User;
use App\Models\Address;
use Stripe\StripeClient;
use App\Models\CMS\Country;
use Illuminate\Support\Str;
use App\Models\Catalog\Meal;
use App\Models\CMS\Province;
use Illuminate\Http\Request;
use App\Models\Sales\CheckoutLink;
use App\Models\Catalog\MealPackage;
use App\Http\Controllers\Controller;
use App\Models\Catalog\MealPackagePrice;
use App\Http\Requests\Sales\CheckoutLinkStoreRequest;

class PaymentLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (request()->ajax()) {
            $query = CheckoutLink::query()->withJoins()->withSelection();

            return datatables()->of($query)

                ->editColumn('meal_name', function ($row) {
                    return '<div class="d-flex flex-column">
                        <div class="">' . $row->meal_name . '</div>
                        <div class="text-sm">' . $row->package_name . '(' . $row->package_tagline . ')' . '</div>
                    </div>';
                })

                ->addColumn(
                    'recurring_status',
                    function ($row) {
                        return $row->is_recurring ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa fa-times-circle text-danger"> </i>';
                    }
                )

                ->editColumn('status', function ($row) {

                    if ($row->status == 'paid') {
                        $status = '<span class="badge badge-success">Paid</span>';
                    } elseif ($row->status == 'pending') {
                        $status = '<span class="badge badge-warning">Pending</span>';
                    } elseif ($row->status == 'cancelled') {
                        $status = '<span class="badge badge-danger">Cancelled</span>';
                    } else {
                        $status = '<span class="badge badge-dark">' . $row->status . '</span>';
                    }

                    return $status;
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at?->format('d-M-Y  h:m A');
                })
                ->addColumn('action', function ($row) {
                    $compact['row']         = $row;
                    $compact['showUrl']     = route('admin.sales.payment-links.show', $row->id);
                    // $compact['editUrl']      = route('admin.sales.payment-links.edit', $row->id);
                    // $dara['deleteUrl']       = route('admin.sales.payment-links.destroy', $row->id);
                    // $compact['restoreUrl']   = route('admin.sales.payment-links.restore', $row->id);


                    return view('theme.adminlte.components._table-actions', $compact)->render();
                })

                ->rawColumns(['meal_name', 'recurring_status', 'status', 'action'])
                ->make(true);
        }


        return view('theme.adminlte.sales.checkout-links.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $checkoutLink = CheckoutLink::findOrFail($id);

        $data['checkoutLink'] = $checkoutLink;

        return view('theme.adminlte.sales.checkout-links.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

    // STEP 2: Return Packages for selected Meal
    public function getPackages(Meal $meal)
    {
        $packages = $meal->mealPackages()
            ->with('package:id,name,thumbnail,tagline')
            ->get();

        $view = view('theme.adminlte.sales.checkout-links.ajax.packages', compact('packages', 'meal'))->render();

        return response()->json([
            'success' => true,
            'html' => $view
        ]);
    }

    // STEP 3: Return Prices for selected Package
    public function getPrices(MealPackage $mealPackage)
    {
        $prices = MealPackagePrice::with('calorie:id,label')
            ->where('meal_package_id', $mealPackage->id)
            ->get();

        $view = view('theme.adminlte.sales.checkout-links.ajax.prices', compact('prices', 'mealPackage'))->render();

        return response()->json([
            'success' => true,
            'html' => $view
        ]);
    }

    // STEP 5: Return Review Summary (Final Step)
    public function getReview()
    {
        $data = [
            'meal_name' => request('meal_name'),
            'package_name' => request('package_name'),
            'price' => request('price'),
            'duration' => request('duration'),
            'calorie' => request('calorie'),
            'customer_name' => request('customer_name'),
            'customer_email' => request('customer_email'),
            'customer_phone' => request('customer_phone'),
            'country' => request('country'),
            'province' => request('province'),
            'city' => request('city'),
        ];

        $view = view('theme.adminlte.sales.checkout-links.ajax.summary', $data)->render();

        return response()->json([
            'success' => true,
            'html' => $view
        ]);
    }

    // Find customer by exact email or quick search
    public function findCustomer(Request $request)
    {
        $email = $request->get('email');
        $q     = $request->get('q');

        if ($email) {
            $user = User::where('email', $email)->first();
            return response()->json([
                'found'    => (bool)$user,
                'customer' => $user,
            ]);
        }

        if ($q) {
            $users = User::where('email', 'like', "%$q%")
                ->orWhere('name', 'like', "%$q%")
                ->limit(10)->get(['id', 'name', 'email']);
            return response()->json(['results' => $users]);
        }

        return response()->json(['results' => []]);
    }

    public function getCustomerAddresses(User $user)
    {
        $addresses = $user->addresses()->with(['province:id,name', 'city:id,name', 'area:id,name'])->get();
        return response()->json(['success' => true, 'addresses' => $addresses]);
    }

    public function storeCustomerAddress(Request $request, User $user)
    {
        $data = $request->validate([
            'province_id' => ['required', 'exists:provinces,id'],
            'city_id'     => ['required', 'exists:cities,id'],
            'area_id'     => ['required', 'exists:areas,id'],
            'address'     => ['required', 'string', 'max:255'],
            'landmark'    => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
        ]);

        $addr = $user->addresses()->create($data);

        return response()->json(['success' => true, 'address' => $addr->load(['province:id,name', 'city:id,name', 'area:id,name'])]);
    }

    // Build summary with validation
    public function getSummary(Request $request)
    {
        // If existing customer selected, we expect customer_id (and optionally address_id)
        // Else we validate new customer fields and ensure email is not a duplicate.
        $isExisting = (bool) $request->get('customer_id');

        $rules = [
            'meal_name'     => ['required', 'string'],
            'package_name'  => ['required', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'duration'      => ['required', 'integer', 'min:1'],
            'calorie'       => ['nullable', 'string'],
        ];

        if ($isExisting) {
            $rules = array_merge($rules, [
                'customer_id' => ['required', 'exists:users,id'],
                // Either address_id or full address fields to create a new one
                'address_id'  => ['nullable', 'exists:addresses,id'],
                'province_id' => ['required_without:address_id', 'exists:provinces,id'],
                'city_id'     => ['required_without:address_id', 'exists:cities,id'],
                'area_id'     => ['required_without:address_id', 'exists:areas,id'],
                'address'     => ['required_without:address_id', 'string', 'max:255'],
                'landmark'    => ['nullable', 'string', 'max:255'],
            ]);
        } else {
            $rules = array_merge($rules, [
                'customer_name'  => ['required', 'string', 'max:255'],
                'customer_email' => ['required', 'email', Rule::unique('users', 'email')],
                'customer_phone' => ['required', 'string', 'max:50'],
                'province_id'    => ['required', 'exists:provinces,id'],
                'city_id'        => ['required', 'exists:cities,id'],
                'area_id'        => ['required', 'exists:areas,id'],
                'address'        => ['required', 'string', 'max:255'],
                'landmark'       => ['nullable', 'string', 'max:255'],
            ]);
        }

        $data = $request->validate($rules);

        // Build display info (if address_id given, load it; else build from fields)
        $customerName  = $isExisting ? optional(User::find($request->customer_id))->name : $request->customer_name;
        $customerEmail = $isExisting ? optional(User::find($request->customer_id))->email : $request->customer_email;
        $customerPhone = $isExisting ? optional(User::find($request->customer_id))->phone : $request->customer_phone;

        if ($request->filled('address_id')) {
            $addr = Address::with(['province:id,name', 'city:id,name', 'area:id,name'])->find($request->address_id);
            $addressLine = sprintf('%s, %s, %s', $addr->address, $addr->city->name ?? '', $addr->province->name ?? '');
        } else {
            $addressLine = $request->address;
        }

        $view = view('theme.adminlte.sales.checkout-link.ajax.summary', [
            'meal_name'     => $request->meal_name,
            'package_name'  => $request->package_name,
            'price'         => $request->price,
            'duration'      => $request->duration,
            'calorie'       => $request->calorie,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_phone' => $customerPhone,
            'address_line'  => $addressLine,
        ])->render();

        return response()->json(['success' => true, 'html' => $view]);
    }
}
