<?php

namespace App\Http\Controllers\Admin\Sales;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Sales\Subscription;
use App\Http\Controllers\Controller;
use App\Models\Catalog\Meal;
use App\Models\Catalog\MealPackagePrice;
use App\Models\Catalog\Package;
use App\Models\CMS\City;
use App\Models\CMS\Province;
use App\Services\StripeService;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Subscription::query()
                ->leftJoin('users', 'users.id', '=', 'subscriptions.user_id')
                ->leftJoin('meal_package_prices', 'meal_package_prices.id', '=', 'subscriptions.meal_package_price_id')
                ->leftJoin('calories', 'calories.id', '=', 'meal_package_prices.calorie_id')
                ->leftJoin('meal_packages', 'meal_packages.id', '=', 'subscriptions.meal_package_id')
                ->leftJoin('packages', 'packages.id', '=', 'meal_packages.package_id')
                ->leftJoin('meals', 'meals.id', '=', 'meal_packages.meal_id')
                ->with(['address'])
                ->select(
                    'subscriptions.*',
                    'users.name as user_name',
                    'users.phone as user_phone',
                    'meals.name as meal_name',
                    'packages.name as package_name',
                    'packages.tagline as package_tagline',
                    'meal_package_prices.duration',
                    'calories.label as calorie_label',
                )
                ->groupBy('subscriptions.id');

            // ✅ Apply filters
            if ($mealId = request('meal_id')) {
                $query->where('meals.id', $mealId);
            }

            if ($packageId = request('package_id')) {
                $query->where('packages.id', $packageId);
            }

            if ($duration = request('duration')) {
                $query->where('meal_package_prices.duration', $duration);
            }

            if (!is_null(request('auto_charge')) && request('auto_charge') !== '') {
                $query->where('subscriptions.auto_charge', request('auto_charge'));
            }

            if ($status = request('status')) {
                $query->where('subscriptions.status', $status);
            }

            return datatables()->of($query)
                ->editColumn(
                    'user_name',
                    fn($row) => '
                <div class="d-flex flex-column">
                  <div>' . e($row->user_name) . '</div>
                  <div class="text-sm">(' . e($row->user_phone) . ')</div>
                </div>'
                )
                ->editColumn(
                    'meal_name',
                    fn($row) => '
                <div class="d-flex flex-column">
                  <div>' . e($row->meal_name) . '</div>
                  <div class="text-sm">' . e($row->package_name) . ' (' . e($row->package_tagline) . ')</div>
                </div>'
                )
                ->editColumn('status', function ($row) {
                    return match ($row->status) {
                        'active' => '<span class="badge badge-success">Active</span>',
                        'pending' => '<span class="badge badge-warning">Pending</span>',
                        'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
                        'paused' => '<span class="badge badge-info">Paused</span>',
                        'payment_failed' => '<span class="badge badge-dark">Payment Failed</span>',
                        default => '<span class="badge badge-secondary">' . e($row->status) . '</span>',
                    };
                })
                ->editColumn('duration', fn($row) => $row->duration . ' ' . Str::plural('Day', $row->duration))
                ->addColumn('auto_charge_status', fn($row) => $row->auto_charge ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times-circle text-danger"></i>')
                ->editColumn('start_date', fn($row) => optional($row->start_date)->format('d-M-Y'))
                ->editColumn('end_date', fn($row) => optional($row->end_date)->format('d-M-Y'))
                ->editColumn('created_at', fn($row) => optional($row->created_at)->format('d-M-Y h:i A'))
                ->addColumn('delivery_address', fn($row) => $row->address?->render() ?? 'N/A')
                ->addColumn('action', function ($row) {
                    return view('theme.adminlte.components._table-actions', [
                        'row' => $row,
                        'showUrl' => route('admin.sales.subscriptions.show', $row->id),
                    ])->render();
                })
                ->rawColumns(['user_name', 'meal_name', 'auto_charge_status', 'delivery_address', 'status', 'action'])
                ->make(true);
        }

        $data['meals'] = Meal::active()->get();
        $data['packages'] = Package::active()->get();
        $data['durations'] = MealPackagePrice::select('duration')->distinct()->get();

        return view('theme.adminlte.sales.subscriptions.index', $data);
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
        $subscription = Subscription::with([
            'user.defaultAddress.province',
            'user.defaultAddress.country',
            'mealPackage.meal',
            'mealPackage.package',
            'mealPackagePrice.calorie',
        ])->findOrFail($id);

        if ($subscription->reference == null) {
            $paymentIntentId = (new StripeService())->getPaymentIntentId($subscription->stripe_id);
            $subscription->update(['reference' => $paymentIntentId]);
        }

        return view('theme.adminlte.sales.subscriptions.show', compact('subscription'));
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
        $subscription = Subscription::findOrFail($id);
        $subscription->update(['status' => 'cancelled', 'ends_at' => $subscription->ends_at ?? now()]);

        return response()->json([
            'success' => true,
            'title' => 'Cancelled.',
            'message' => 'Subscription Cancelled Successfully.',
            'redirect' => route('admin.sales.subscriptions.show', [$subscription])
        ]);
    }

}
