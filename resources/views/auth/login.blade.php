@extends('layout.app')
@section('content')

<nav class="bg-white border-bottom py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <img src="{{ asset('photo/logo.png') }}" class="brand-logo" alt="PHLC Logo" style="width: 55px; height: 55px;">
      <div>
        <div class="fw-bold text-navy" style="font-size:15px;line-height:1.2">PHLC</div>
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
      <h3 class="fw-bold mb-1" style="color:#1e293b">Welcome Back</h3>
      <p class="text-muted" style="font-size:13.5px">Sign in to access your account</p>
    </div>

    {{-- Tab switcher --}}
    <div class="bg-light rounded-3 d-flex p-1 mb-4">
      <button type="button" class="btn flex-fill rounded-2 fw-semibold login-tab-btn {{ $activeTab === 'admin' ? '' : 'active' }}" id="tab-parent" onclick="switchLoginTab('parent')" style="font-size:14px">Parent</button>
      <button type="button" class="btn flex-fill rounded-2 fw-semibold login-tab-btn {{ $activeTab === 'admin' ? 'active' : '' }}" id="tab-admin"  onclick="switchLoginTab('admin')"  style="font-size:14px">Admin</button>
    </div>

    {{-- ── PARENT PANEL ─────────────────────────────────────────────── --}}
    <div id="login-parent-panel" class="{{ $activeTab === 'admin' ? 'd-none' : '' }}">
      <form action="{{ route('login.store') }}" method="POST" id="parent-form">
        @csrf
        {{-- NO role hidden field → controller treats it as parent --}}

        <p class="fw-bold mb-1" style="font-size:15px;color:#1e293b">Parent Login</p>
        <p class="text-muted mb-3" style="font-size:13px">Enter your credentials to access the parent portal</p>

        <div class="mb-3">
          <label class="form-label fw-semibold" style="font-size:13px">Email Address</label>
          <input type="email" name="email" value="{{ $activeTab !== 'admin' ? old('email') : '' }}"
                 class="form-control @if($activeTab !== 'admin' && $errors->has('email')) is-invalid @endif"
                 placeholder="Enter your email address" autocomplete="email">
          @if($activeTab !== 'admin')
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          @endif
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-semibold mb-0" style="font-size:13px">Password</label>
            <a href="{{ route('password.request') }}" class="text-decoration-none text-cyan" style="font-size:13px">Forgot password?</a>
          </div>
          <div class="position-relative">
            <input type="password" name="password"
                   class="form-control pe-5 @if($activeTab !== 'admin' && $errors->has('password')) is-invalid @endif"
                   id="stu-pw" placeholder="Enter your password" autocomplete="current-password">
            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-1 p-1 text-secondary border-0" onclick="togglePw('stu-pw',this)" tabindex="-1">
              <i class="bi bi-eye"></i>
            </button>
            @if($activeTab !== 'admin')
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            @endif
          </div>
        </div>

        <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>

        <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold">Login to Parent Portal</button>
        <p class="text-center text-muted mt-3 mb-0" style="font-size:13px">
          Don't have an account? <a href="{{ route('register') }}" class="text-cyan text-decoration-none fw-medium">Apply for admission</a>
        </p>
      </form>
    </div>

    {{-- ── ADMIN PANEL ──────────────────────────────────────────────── --}}
    <div id="login-admin-panel" class="{{ $activeTab === 'admin' ? '' : 'd-none' }}">
      <form action="{{ route('login.store') }}" method="POST" id="admin-form">
        @csrf
        <input type="hidden" name="role" value="admin">

        <p class="fw-bold mb-1" style="font-size:15px;color:#1e293b">Admin Login</p>
        <p class="text-muted mb-3" style="font-size:13px">Enter your credentials to access the admin dashboard</p>

        <div class="mb-3">
          <label class="form-label fw-semibold" style="font-size:13px">Email Address</label>
          <input type="email" name="email" value="{{ $activeTab === 'admin' ? old('email') : '' }}"
                 class="form-control @if($activeTab === 'admin' && $errors->has('email')) is-invalid @endif"
                 placeholder="admin@school.edu" autocomplete="email">
          @if($activeTab === 'admin')
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          @endif
        </div>

        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label fw-semibold mb-0" style="font-size:13px">Password</label>
            <a href="{{ route('password.request') }}" class="text-decoration-none text-cyan" style="font-size:13px">Forgot password?</a>
          </div>
          <div class="position-relative">
            <input type="password" name="password"
                   class="form-control pe-5 @if($activeTab === 'admin' && $errors->has('password')) is-invalid @endif"
                   id="adm-pw" placeholder="Enter your password" autocomplete="current-password">
            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-1 p-1 text-secondary border-0" onclick="togglePw('adm-pw',this)" tabindex="-1">
              <i class="bi bi-eye"></i>
            </button>
            @if($activeTab === 'admin')
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            @endif
          </div>
        </div>

        <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>

        <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold">Login to Admin Dashboard</button>
      </form>
    </div>

  </div>
</div>

@endsection

@push('script')
{{-- reCAPTCHA script already loaded once, globally, by layout/app.blade.php --}}
<script>
  function switchLoginTab(tab) {
    const isAdmin = tab === 'admin';

    // Buttons
    document.getElementById('tab-parent').classList.toggle('active', !isAdmin);
    document.getElementById('tab-admin').classList.toggle('active',   isAdmin);

    // Panels
    document.getElementById('login-parent-panel').classList.toggle('d-none',  isAdmin);
    document.getElementById('login-admin-panel').classList.toggle('d-none',  !isAdmin);
  }

  function togglePw(id, btn) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') {
      inp.type = 'text';
      btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
      inp.type = 'password';
      btn.innerHTML = '<i class="bi bi-eye"></i>';
    }
  }
</script>
@endpush