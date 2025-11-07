<?php

namespace App\Http\Controllers\Admin\Catalog;

use Illuminate\Http\Request;
use App\Models\Catalog\Calorie;
use Illuminate\Support\Facades\DB;
use App\Models\Catalog\MealPackage;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\Catalog\MealPackagePriceStoreRequest;
use App\Http\Requests\Catalog\MealPackagePriceUpdateRequest;
use App\Models\Catalog\MealPackagePrice;
use App\Services\StripeService;

class MealPackagePriceController extends Controller
{
    use AuthorizesRequests;

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
    public function create(string $mealId, string $packageId)
    {
        $this->authorize('create', MealPackage::class);

        $mealPackage            = MealPackage::where('meal_id', $mealId)->where('package_id', $packageId)->firstOrFail();
        $calories               = Calorie::active()->get();
        $data['mealPackage']    = $mealPackage;
        $data['calories']       = $calories;

        $response['view']       = view('theme.adminlte.catalog.meals.packages.prices.create', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(string $mealId, string $packageId, MealPackagePriceStoreRequest $request)
    {
        $this->authorize('create', MealPackage::class);

        $validated = $request->validated();

        $mealPackage  = MealPackage::where('meal_id', $mealId)->where('package_id', $packageId)->firstOrFail();

        $exists = MealPackagePrice::where('meal_package_id', $mealPackage->id)
            ->where('calorie_id', $validated['calorie_id'])
            ->where('duration', $validated['duration'])
            ->exists();


        $exists = MealPackagePrice::isDuplicate($mealPackage->id, $validated['calorie_id'], $validated['duration']);

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This price already exists for selected calorie and duration.',
            ], 422);
        }

        DB::beginTransaction();
        try {

            if (!empty($validated['is_active'])) {
                $validated['is_active'] = true;
            } else {
                $validated['is_active'] = false;
            }

            $validated['meal_package_id'] = $mealPackage->id;

            $price = MealPackagePrice::create($validated);

            (new StripeService())->createPrice($price);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => __('crud.created', ['name' => 'Price']),
            'redirect' => route('admin.catalog.meals.edit', ['meal' => $mealId])
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
    public function edit(string $mealId, string $packageId, string $id)
    {
        $mealPackage    = MealPackage::where('meal_id', $mealId)->where('package_id', $packageId)->firstOrFail();
        $price          = MealPackagePrice::where('meal_package_id', $mealPackage->id)->findOrFail($id);
        $calories = Calorie::active()->get();

        $data['price']      = $price;
        $data['calories']   = $calories;

        $response['view'] = view('theme.adminlte.catalog.meals.packages.prices.edit', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(string $mealId, string $packageId, MealPackagePriceUpdateRequest $request, string $id)
    {
        $mealPackage    = MealPackage::where('meal_id', $mealId)->where('package_id', $packageId)->firstOrFail();
        $price          = MealPackagePrice::where('meal_package_id', $mealPackage->id)->findOrFail($id);

        $validated      = $request->validated();

        $exists         = MealPackagePrice::isDuplicate($mealPackage->id, $validated['calorie_id'], $validated['duration'], $id);

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Duplicate entry for this calorie and duration combination.',
            ], 422);
        }

        $validated['is_active'] = !empty($validated['is_active']);

        $oldPrice = $price->amount;
        $price->update($validated);

        // Check if price amount has changed
        if ($oldPrice != $price->amount) {
            $price->stripe_price_id = null;
            $price->save();
        }

        (new StripeService())->createPrice($price);

        return response()->json([
            'success' => true,
            'message' => __('crud.updated', ['name' => 'Price']),
            'redirect' => route('admin.catalog.meals.edit', ['meal' => $mealId]),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $mealId, string $packageId, string $id)
    {
        //
    }
}
