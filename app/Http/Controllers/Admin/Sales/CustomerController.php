<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Models\User;
use App\Models\CMS\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\CustomerStoreRequest;
use App\Models\Address;
use App\Models\Catalog\Meal;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (request()->ajax()) {
            $mealColors = Meal::pluck('color', 'name')->toArray();


            $query = User::leftJoin('subscriptions', function ($join) {
                $join->on('subscriptions.user_id', 'users.id')->whereIn('status', ['active', 'paused']);
            })
                ->leftJoin('meal_packages', 'meal_packages.id', 'subscriptions.meal_package_id')
                ->leftJoin('meals', 'meals.id', 'meal_packages.meal_id')
                ->select(
                    'users.*',
                    DB::raw('GROUP_CONCAT(meals.name) as subscription_names')
                )
                ->groupBy('users.id');

            return
                datatables()->of($query)
                ->editColumn('created_at', function ($row) {
                    return $row->created_at?->format('d-M-Y  h:m A');
                })
                ->editColumn('subscription_names', function ($row) use ($mealColors) {
                    if (!$row->subscription_names) {
                        return '<span class="text-muted">No subscriptions</span>';
                    }

                    $subscriptions = explode(',', $row->subscription_names);

                    $html = '';

                    foreach ($subscriptions as $index => $name) {
                        $color = $mealColors[$name];
                        $html .= "<span class='me-1 alert alert-$color' style='padding: .25rem .7rem;' role='alert'>$name</span>";
                    }

                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $compact['row']         = $row;
                    $compact['showUrl']     = route('admin.sales.customers.show', $row->id);
                    // $compact['editUrl']      = route('admin.sales.customers.edit', $row->id);
                    // $dara['deleteUrl']       = route('admin.sales.customers.destroy', $row->id);
                    // $compact['restoreUrl']   = route('admin.sales.customers.restore', $row->id);


                    return view('theme.adminlte.components._table-actions', $compact)->render();
                })

                ->rawColumns(['meal_name', 'subscription_names', 'action'])
                ->make(true);
        }



        return view('theme.adminlte.sales.customers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $this->authorize('create', Meal::class);

        $data = [];
        $data['provinces']      = Province::where('country_id', 1)->get();
        $data['addressTypes']   = Address::getTypes();


        $response['view'] = view('theme.adminlte.sales.customers.create', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerStoreRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'is_active' => false,
                'password' => bcrypt(Str::random(10)),
            ]);

            if (!empty($validated['type'])) {
                $address = $user->addresses()->create([
                    'phone' => $validated['phone'],
                    'type' => $validated['type'],
                    'country_id' => 1, // UAE
                    'province_id' => $validated['province_id'],
                    'city_id' => $validated['city_id'],
                    'area_id' => $validated['area_id'],
                    'address' => $validated['address'],
                    'landmark' => $validated['landmark'] ?? null,
                ]);

                // Set as default address if it's the first one
                if ($user->addresses()->count() === 1) {
                    $user->update(['default_address_id' => $address->id]);
                }
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => __('crud.created', ['name' => 'Customer']),
            'redirect' => route('admin.sales.customers.index')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $data['customer'] = $user;

        return view('theme.adminlte.sales.customers.show', $data);
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
}
