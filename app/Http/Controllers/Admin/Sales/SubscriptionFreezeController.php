<?php

namespace App\Http\Controllers\Admin\Sales;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Sales\Subscription;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\SubscriptionScheduler;
use App\Models\Sales\SubscriptionFreeze;

class SubscriptionFreezeController extends Controller
{

    public function create(Subscription $subscription)
    {
        $data['subscription']   = $subscription;
        $response['view']       = view('theme.adminlte.sales.subscriptions.freezes.create', $data)->render();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function store(Request $request, Subscription $subscription, SubscriptionScheduler $scheduler)
    {
        $data = $request->validate([
            'freeze_start_date' => ['required', 'date', 'after_or_equal:today'],
            'freeze_end_date'   => ['required', 'date', 'after_or_equal:freeze_start_date'],
            'reason'            => ['nullable', 'string', 'max:255'],
        ]);

        $freeze = $scheduler->scheduleFreeze(
            $subscription,
            Carbon::parse($data['freeze_start_date']),
            Carbon::parse($data['freeze_end_date']),
            $data['reason'] ?? null,
            auth('admin')->id() ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Freeze scheduled successfully.',
            'redirect' => route('admin.sales.subscriptions.show', $subscription)
        ]);
    }

    public function destroy(Subscription $subscription, SubscriptionFreeze $freeze, SubscriptionScheduler $scheduler)
    {
        if (!in_array($freeze->status, ['scheduled'])) {
            return back()->with('error', 'Only scheduled freezes can be cancelled.');
        }

        DB::beginTransaction();

        try {
            $frozenDays = $freeze->frozen_days ?? 0;

            if ($frozenDays > 0) {
                $scheduler->pushSchedule($subscription, -$frozenDays);
            }

            $freeze->update(['status' => 'cancelled']);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return response()->json([
            'success' => true,
            'message' => 'Freeze cancelled successfully.',
            'redirect' => route('admin.sales.subscriptions.show', $subscription)
        ]);
    }
}
