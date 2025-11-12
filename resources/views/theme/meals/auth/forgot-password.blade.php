@extends('theme.meals.auth.layouts.app')

@section('title', 'Forgot Password')
@section('subtitle', 'Reset your access to daily nutrition.')

@section('content')

  @if (session('status'))
    <div style="background:rgba(255,255,255,0.2);padding:8px;border-radius:6px;margin-bottom:10px;">
      {{ session('status') }}
    </div>
  @endif

  <form method="POST" action="{{ route('password.email') }}">
    @csrf

    <p style="font-size:0.9rem;color:#fff;opacity:0.9;margin-bottom:20px;">
      Forgot your password? No worries — enter your registered email below and we’ll send you a reset link.
    </p>

    <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus>
    @error('email')
      <small style="color:#ffdede">{{ $message }}</small>
    @enderror

    <button type="submit" class="btn">Send Password Reset Link</button>

    <div class="extras">
      <a href="{{ route('login') }}"><i class="fas fa-arrow-left me-1"></i> Back to Login</a>
    </div>
  </form>

@endsection
