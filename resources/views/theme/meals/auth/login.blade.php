@extends('theme.meals.auth.layouts.app')

@section('title', 'Login')
@section('subtitle', 'Your Daily Dose of Nutrition.')

@section('content')
  @if (session('status'))
    <div style="background:rgba(255,255,255,0.2);padding:8px;border-radius:6px;margin-bottom:10px;">
      {{ session('status') }}
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf

    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus>
    @error('email')
      <small style="color:#ffdede">{{ $message }}</small>
    @enderror

    <input type="password" name="password" placeholder="Password" required>
    @error('password')
      <small style="color:#ffdede">{{ $message }}</small>
    @enderror

    <button type="submit" class="btn">Login</button>

    <div class="extras">
      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">Forgot Password?</a>
      @endif
      @if (Route::has('register'))
        <a href="{{ route('register') }}">Register</a>
      @endif
    </div>
  </form>
@endsection
