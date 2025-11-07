<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Cart\Order;
use Illuminate\Http\Request;
use App\Models\Catalog\Product;
use App\Models\Cart\OrderLineItem;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    function dashboard()
    {
        return view('theme.adminlte.dashboard');
    }
}
