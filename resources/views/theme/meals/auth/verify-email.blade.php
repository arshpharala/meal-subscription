@extends('theme.meals.auth.layouts.app')

@section('title', 'Verify Email')
@section('subtitle', 'Please verify your email address to continue.')

@section('content')
  @if (session('status') == 'verification-link-sent')
    <div style="background:rgba(255,255,255,0.2);padding:10px;border-radius:8px;margin-bottom:15px;">
      A new verification link has been sent to your email.
    </div>
  @endif

  <form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit" class="btn">Resend Verification Email</button>
  </form>

  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn" style="margin-top:10px;">Log Out</button>
  </form>

  <p style="margin-top:15px; font-size:0.9rem; color:rgba(255,255,255,0.9);">
    Didn’t receive the email? Check your spam folder.
  </p>
@endsection
