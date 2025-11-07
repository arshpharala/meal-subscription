<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Models\User;
use App\Models\Catalog\Meal;
use Illuminate\Http\Request;
use App\Models\Catalog\Package;
use App\Models\Sales\CheckoutLink;
use App\Models\Catalog\MealPackage;
use App\Http\Controllers\Controller;
use App\Models\Catalog\MealPackagePrice;
use App\Models\CMS\Tax;
use App\Models\Sales\Subscription;
use App\Services\PaymentLinkService;
use App\Services\SubscriptionCheckoutService;
use Illuminate\Support\Facades\DB;

class CustomerSubscriptionController extends Controller
{

    public function create(string $customerId)
    {
        $customer = User::findOrFail($customerId);

        if ($customer->addresses->count() === 0) {
            return redirect()
                ->route('admin.sales.customers.show', $customer->id)
                ->with('error', 'Please add at least one address before creating a payment link.');
        }

        $data['customer'] = $customer;
        $data['meals'] = Meal::active()->withJoins()->withSelection()->get();

        return view('theme.adminlte.sales.customers.subscriptions.create', $data);
    }

    public function store(Request $request, string $customerId)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'meal_package_price_id' => 'required|exists:meal_package_prices,id',
            'start_date' => 'nullable|date',
            'is_recurring' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $customer = User::findOrFail($customerId);
            $service = new PaymentLinkService();
            $result = $service->create($customer, $request->all());
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => 'Checkout link generated successfully.',
            'redirect' => route('admin.sales.payment-links.show', $result['checkout_id'])
        ]);
    }


    public function destroy(string $customerId, string $id)
    {
        $subscription = Subscription::where('user_id', $customerId)->findOrFail($id);

        $subscription->update(['status' => 'cancelled', 'ends_at' => $subscription->ends_at ?? now()]);

        return response()->json([
            'success' => true,
            'title' => 'Cancelled.',
            'message' => 'Subscription Cancelled Successfully.',
            'redirect' => route('admin.sales.customers.show', $customerId)
        ]);
    }

    // Existing AJAX step endpoints remain the same
    public function getPackages($mealId)
    {
        $meal = Meal::findOrFail($mealId);
        $packages = $meal->mealPackages()->with('package:id,name,thumbnail,tagline')->get();

        $view = view('theme.adminlte.sales.customers.subscriptions.ajax.packages', compact('packages', 'meal'))->render();
        return response()->json(['success' => true, 'html' => $view]);
    }

    public function getPrices(Meal $meal, Package $package)
    {
        $mealPackage = MealPackage::where('meal_id', $meal->id)->where('package_id', $package->id)->firstOrFail();
        $prices = MealPackagePrice::with('calorie:id,label')->where('meal_package_id', $mealPackage->id)->get();

        $view = view('theme.adminlte.sales.customers.subscriptions.ajax.prices', compact('meal', 'package', 'prices', 'mealPackage'))->render();
        return response()->json(['success' => true, 'html' => $view]);
    }

    public function getSummary(string $customerId, string $priceId)
    {
        $customer = User::findOrFail($customerId);
        $mealPackagePrice = MealPackagePrice::findOrFail($priceId);
        $mealPackage = $mealPackagePrice->mealPackage;
        $meal = $mealPackage->meal;
        $package = $mealPackage->package;
        $taxes = Tax::where('is_active', 1)->get();


        $view = view('theme.adminlte.sales.customers.subscriptions.ajax.summary', compact('customer', 'meal', 'package', 'mealPackagePrice', 'mealPackage', 'taxes'))->render();
        return response()->json(['success' => true, 'html' => $view]);
    }
}
