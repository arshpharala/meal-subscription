<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Catalog\Meal;
use App\Models\Catalog\Package;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        $restaurantName = \App\Models\Setting::get('restaurant_name', 'Our Restaurant');
        $address = \App\Models\Setting::get('address', '123 Main St, City, Country');
        $phone = \App\Models\Setting::get('phone', '+1 234 567 890');
        $email = \App\Models\Setting::get('email', 'info@example.com');

        return view('theme.meals.pages.home', compact('restaurantName', 'address', 'phone', 'email'));
    }

    public function contact()
    {
        return view('theme.meals.pages.contact');
    }

    public function meals(Request $request)
    {
        $query = Meal::query()
            ->where('is_active', true)
            ->withJoins()
            ->withSelection();

        // Apply package filter
        if ($request->has('packages')) {
            $query->whereHas('packages', function ($q) use ($request) {
                $q->whereIn('packages.id', $request->packages);
            });
        }

        // Apply sorting
        switch ($request->get('sort', 'position')) {
            case 'name':
                $query->orderBy('name');
                break;
            case 'created_at':
                $query->latest();
                break;
            default:
                $query->orderBy('position');
                break;
        }

        $meals = $query->paginate(12);
        $packages = Package::where('is_active', true)->orderBy('name')->get();

        return view('theme.meals.pages.meals', compact('meals', 'packages'));
    }

    public function mealDetails($slug)
    {
        $meal = Meal::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->withJoins()
            ->withSelection()
            ->firstOrFail();

        return view('theme.meals.pages.meal-details', compact('meal'));
    }
}
