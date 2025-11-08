<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Models\Catalog\Meal;
use Illuminate\Http\Request;
use App\Models\Catalog\Package;
use App\Services\StripeService;
use App\Models\Catalog\MealPackage;
use App\Http\Controllers\Controller;
use App\Models\Catalog\MealPackagePrice;
use App\Services\Stripe\StripeCatalogService;
use App\Http\Requests\Catalog\MealPackageStoreRequest;

class MealPackageController extends Controller
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
    public function create(string $mealId)
    {
        $meal               = Meal::findOrFail($mealId);
        $selectedPackageIds = $meal->packages->pluck('id')->toArray();
        $packages           = Package::active()->whereNotIn('id', $selectedPackageIds)->get();

        $data['meal']       = $meal;
        $data['packages']   = $packages;

        $response['view']   = view('theme.adminlte.catalog.meals.packages.create', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MealPackageStoreRequest $request, string $mealId)
    {
        $meal   = Meal::findOrFail($mealId);

        $validated = $request->validated();

        $stripeService = new StripeCatalogService();

        foreach ($validated['packages'] ?? [] as $key => $packageId) {
            $mealPackage            = MealPackage::firstOrNew(['meal_id' => $meal->id, 'package_id' => $packageId]);
            $mealPackage->code      = $validated['code'] ?? null;
            $mealPackage->is_active = true;
            $mealPackage->save();

            $stripeService->createProduct($mealPackage);
        }


        return response()->json([
            'success'   => true,
            'message'   => __('crud.created', ['name' => 'Meal Package']),
            'redirect' => route('admin.catalog.meals.edit', $meal)
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
    public function destroy(string $mealId, string $packageId)
    {
        $mealPackage    = MealPackage::where('meal_id', $mealId)->where('package_id', $packageId)->firstOrFail();

        if (request()->filled('status')) {

            if (request()->status == 1) {
                $mealPackage->is_active = true;
            } else {
                $mealPackage->is_active = false;
            }
            $mealPackage->save();
        }


        return response()->json([
            'success' => true,
            'title' => 'Updated',
            'message' => __('crud.updated', ['name' => 'Status']),
            'redirect' => route('admin.catalog.meals.edit', ['meal' => $mealId]),
        ]);
    }
}
