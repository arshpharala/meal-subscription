@extends('theme.meals.auth.layouts.app')

@section('title', 'Register')
@section('subtitle', 'Create your account to continue')

@section('content')
  <form method="POST" action="{{ route('register') }}">
    @csrf

    <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Full Name" required autofocus>
    @error('name')
      <small style="color:#ffdede">{{ $message }}</small>
    @enderror

    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required>
    @error('email')
      <small style="color:#ffdede">{{ $message }}</small>
    @enderror

    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="+971..." required>
    @error('phone')
      <small style="color:#ffdede">{{ $message }}</small>
    @enderror

    <input id="password" type="password" name="password" placeholder="Password" required>
    @error('password')
      <small style="color:#ffdede">{{ $message }}</small>
    @enderror

    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm Password"
      required>

    <button type="submit" class="btn">Register</button>

    <div class="extras" style="justify-content: center; margin-top: 12px;">
      <a href="{{ route('login') }}">Already have an account? Login</a>
    </div>
  </form>
@endsection
