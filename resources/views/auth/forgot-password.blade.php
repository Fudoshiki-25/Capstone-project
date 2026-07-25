@extends('layout.app')
@section('content')

<nav class="bg-white border-bottom py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <img src="{{ asset('photo/logo.png') }}" class="brand-logo" alt="PHLC Logo" style="width: 55px; height: 55px;">
      <div>
        <div class="fw-bold text-navy" style="font-size:15px;line-height:1.2">PHLCI</div>
        <div class="text-muted" style="font-size:11px">Enrollment System</div>
      </div>
    </div>
    <a href="{{ route('landingpage') }}" class="text-decoration-none fw-medium d-flex align-items-center gap-1 text-navy" style="font-size:14px">
      <i class="bi bi-arrow-left"></i> Back to Home
    </a>
  </div>
</nav>

<div class="login-page-bg d-flex align-items-center justify-content-center" style="min-height:calc(100vh - 57px)">
  <div class="bg-white rounded-4 border shadow-sm p-4 p-md-5 w-100" style="max-width:460px;margin:40px auto">

    <div class="text-center mb-4">
      <img src="{{ asset('photo/logo.png') }}" class="brand-logo mx-auto mb-3" style="width:72px;height:72px" alt="PHLC Logo">
      <h3 class="fw-bold mb-1" style="color:#1e293b">Forgot Password?</h3>
      <p class="text-muted" style="font-size:13.5px">Enter your email and we'll send you a link to reset it.</p>
    </div>

    <form action="{{ route('password.email') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label class="form-label fw-semibold" style="font-size:13px">Email Address</label>
        <input type="email" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="Enter your email address" autocomplete="email" autofocus>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>

      <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold">Send Reset Link</button>
      <p class="text-center text-muted mt-3 mb-0" style="font-size:13px">
        <a href="{{ route('login') }}" class="text-cyan text-decoration-none fw-medium">Back to Login</a>
      </p>
    </form>
  </div>
</div>

@endsection
