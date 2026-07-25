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
      <h3 class="fw-bold mb-1" style="color:#1e293b">Reset Your Password</h3>
      <p class="text-muted" style="font-size:13.5px">Choose a new password for your account.</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div class="mb-3">
        <label class="form-label fw-semibold" style="font-size:13px">Email Address</label>
        <input type="email" name="email" value="{{ old('email', $email) }}"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="Enter your email address" autocomplete="email">
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold" style="font-size:13px">New Password</label>
        <div class="position-relative">
          <input type="password" name="password" class="form-control pe-5 @error('password') is-invalid @enderror"
                 id="rp-pw" placeholder="Enter new password" autocomplete="new-password"
                 oninput="updatePasswordChecklist(this.value, 'rp-pw-req')">
          <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-1 p-1 text-secondary border-0" onclick="togglePw('rp-pw',this)" tabindex="-1">
            <i class="bi bi-eye"></i>
          </button>
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @include('partials.password-checklist', ['prefix' => 'rp-pw-req'])
        <div class="form-text mt-1" style="font-size:11.5px">Must also be different from your current password.</div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-semibold" style="font-size:13px">Confirm New Password</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter new password" autocomplete="new-password">
      </div>

      <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold">Reset Password</button>
    </form>
  </div>
</div>

@endsection
