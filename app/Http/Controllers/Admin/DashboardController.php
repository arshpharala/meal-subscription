<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sales\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('theme.adminlte.dashboard');
    }

    /* ========================================================
     |  KPI CARDS
     ======================================================== */
    public function stats()
    {
        // ✅ Active subscriptions running right now
        $active = Subscription::where('status', 'active')->count();

        // ✅ Cancelled
        $cancelled = Subscription::where('status', 'cancelled')->count();

        // ✅ Freezed / Paused
        $freezed = Subscription::where('status', 'paused')->count();

        // ✅ Unpaid or Payment Failed
        $unpaid = Subscription::where('status', 'payment_failed')->count();

        // ✅ Upcoming Paid Subscriptions (future start date, paid)
        $subscribed = Subscription::where('status', 'scheduled')
            // ->whereDate('created_at', '<=', now())
            // ->whereDate('starts_at', '>', now())
            ->count();

        // ✅ Total Revenue
        $revenue = number_format(Subscription::sum('total'), 2);

        return response()->json([
            'active'     => $active,
            'cancelled'  => $cancelled,
            'freezed'    => $freezed,
            'unpaid'     => $unpaid,
            'subscribed' => $subscribed,
            'revenue'    => $revenue,
        ]);
    }

    /* ========================================================
     |  CHART DATA (Monthly / Weekly / Daily)
     ======================================================== */
    public function chart($type)
    {
        switch ($type) {
            case 'month':
                $data = Subscription::select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
                    DB::raw('SUM(total) as total')
                )
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                break;

            case 'week':
                $data = Subscription::select(
                    DB::raw('YEARWEEK(created_at, 1) as period'),
                    DB::raw('SUM(total) as total')
                )
                    ->where('created_at', '>=', now()->subWeeks(8))
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                break;

            default: // day
                $data = Subscription::select(
                    DB::raw('DATE(created_at) as period'),
                    DB::raw('SUM(total) as total')
                )
                    ->where('created_at', '>=', now()->subDays(14))
                    ->groupBy('period')
                    ->orderBy('period')
                    ->get();
                break;
        }

        return response()->json($data);
    }

    /* ========================================================
     |  TABLES (ending, new, freezed, customers)
     ======================================================== */
    public function table($section)
    {
        $now = now();
        $sevenDaysAgo = $now->copy()->subDays(7);
        $next7Days = $now->copy()->addDays(7);

        switch ($section) {
            case 'ending':
                $data = Subscription::with('user')
                    ->whereBetween('ends_at', [$now, $next7Days])
                    ->orderBy('ends_at')
                    ->take(10)
                    ->get();
                break;

            case 'new':
                $data = Subscription::with('user')
                    ->whereBetween('created_at', [$sevenDaysAgo, $now])
                    ->orderByDesc('created_at')
                    ->take(10)
                    ->get();
                break;

            case 'freezed':
                $data = Subscription::with('user')
                    ->where('status', 'paused')
                    ->orderByDesc('updated_at')
                    ->take(10)
                    ->get();
                break;

            case 'customers':
                $data = User::whereBetween('created_at', [$sevenDaysAgo, $now])
                    ->orderByDesc('created_at')
                    ->take(10)
                    ->get(['id', 'name', 'email', 'created_at']);
                break;

            default:
                $data = collect();
        }

        // ✅ DataTables-friendly response
        return response()->json(['data' => $data]);
    }
}
