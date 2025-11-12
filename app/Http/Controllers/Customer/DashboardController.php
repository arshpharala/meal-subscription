<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load(['subscriptions.mealPackage.meal', 'subscriptions.mealPackage.package', 'subscriptions.mealPackagePrice']);
        return view('theme.meals.dashboard', compact('user'));
    }
}
