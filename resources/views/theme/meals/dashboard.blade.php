@extends('theme.meals.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
  <div class="max-w-5xl mx-auto px-5">

    {{-- GREETING --}}
    <div class="mb-6">
      <h1 class="text-xl font-semibold text-gray-800">Hi, {{ Auth::user()->name }} 👋</h1>
      <p class="text-sm text-gray-500">Here’s what’s happening with your account today</p>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid sm:grid-cols-2 gap-6 mb-8">

      {{-- Active Subscription --}}
      @php
        $sub = Auth::user()->subscriptions()->latest('end_date')->first();
      @endphp

      <div class="row">
        <div class="col-md-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition">
              <div>
                <h3 class="text-sm font-semibold text-gray-700 mb-1">Active Subscription</h3>
                <p class="text-2xl font-bold text-orange-500">
                  {{ $sub ? ucfirst($sub->status) : '—' }}
                </p>
                <p class="text-xs text-gray-400 mt-1">
                  {{ $sub ? 'Ends ' . $sub->end_date?->format('d M Y') : 'No plan yet' }}
                </p>
              </div>
              <div class="w-12 h-12 bg-orange-100 text-orange-600 flex items-center justify-center rounded-full">
                <i class="fas fa-box text-lg"></i>
              </div>
            </div>

        </div>

        <div class="col-md-6">
      <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 flex items-center justify-between hover:shadow-md transition">
        <div>
          <h3 class="text-sm font-semibold text-gray-700 mb-1">Days Remaining</h3>
          <p class="text-2xl font-bold text-orange-500">
            {{ $sub ? max(0, $sub->end_date?->diffInDays(now()) ?? 0) : '0' }}
          </p>
          <p class="text-xs text-gray-400 mt-1">
            {{ $sub ? 'Until ' . $sub->end_date?->format('d M Y') : '—' }}
          </p>
        </div>
        <div class="w-12 h-12 bg-orange-100 text-orange-600 flex items-center justify-center rounded-full">
          <i class="fas fa-calendar-day text-lg"></i>
        </div>
      </div>
        </div>
      </div>

      {{-- Days Remaining --}}

    </div>

    {{-- YOUR SUBSCRIPTION DETAILS --}}
    <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-6 mb-10">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-base font-semibold text-gray-800">
          <i class="fas fa-receipt text-orange-500 mr-2"></i> Your Subscription
        </h2>
        @if($sub)
          <a href="{{ route('customer.subscriptions.show', $sub->id) }}"
            class="text-orange-500 hover:text-orange-600 text-sm font-medium">View Details <i class="fas fa-arrow-right ml-1"></i></a>
        @endif
      </div>

      @if($sub)
        <div class="grid sm:grid-cols-2 gap-3 text-sm text-gray-600">
          <p><span class="font-medium text-gray-800">Meal Plan:</span> {{ $sub->mealPackage->meal->name ?? '-' }}</p>
          <p><span class="font-medium text-gray-800">Package:</span> {{ $sub->mealPackage->package->name ?? '-' }}</p>
          <p><span class="font-medium text-gray-800">Duration:</span> {{ $sub->mealPackagePrice->duration ?? 0 }} Days</p>
          <p><span class="font-medium text-gray-800">Ends On:</span> {{ $sub->end_date?->format('d M Y') ?? '-' }}</p>
        </div>

        <div class="flex items-center justify-between mt-4">
          <span class="text-xs px-2 py-1 rounded-full
            @if($sub->status=='active') bg-green-100 text-green-700
            @elseif($sub->status=='paused') bg-yellow-100 text-yellow-700
            @else bg-gray-100 text-gray-600 @endif">
            {{ ucfirst($sub->status) }}
          </span>
          <a href="{{ route('customer.subscriptions.show', $sub->id) }}"
             class="text-orange-500 hover:underline text-sm font-medium">Manage →</a>
        </div>
      @else
        <div class="flex items-center justify-between flex-wrap">
          <div>
            <p class="text-sm text-gray-600">You haven’t subscribed to any plan yet.</p>
            <p class="text-xs text-gray-400 mt-1">Once your plan starts, details will appear here.</p>
          </div>
          <img src="{{ asset('theme/meals/assets/images/meal-box.png') }}" class="w-20 opacity-70 mt-3 sm:mt-0" alt="Meal Box">
        </div>
      @endif
    </div>


  </div>
</div>
@endsection
