@extends('layout.app')
@section('content')


<nav class="bg-white border-bottom sticky-top py-2">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <img src="{{ asset('photo/logo.png') }}" class="brand-logo" alt="DPNHS Logo" style="width: 55px; height: 55px;">
      <div>
        <div class="fw-bold text-navy" style="font-size:15px;line-height:1.2">DPNHS</div>
        <div class="text-muted" style="font-size:11px">Enrollment System</div>
      </div>
    </div>
    <a href="{{ route('landingpage') }}" class="text-decoration-none fw-medium d-flex align-items-center gap-1 text-navy" style="font-size:14px">
      <i class="bi bi-arrow-left"></i> Back to Home
    </a>
  </div>
</nav>

<form action="{{ route('register.store') }}" method="POST">
  @csrf

<div class="login-page-bg d-flex align-items-center justify-content-center" style="min-height:calc(100vh - 57px)">
  <div class="bg-white rounded-4 border shadow-sm p-4 p-md-5 w-100" style="max-width:460px;margin:40px auto">

    <div class="text-center mb-4">
      <img src="{{ asset('photo/logo.png') }}" class="brand-logo mx-auto mb-3" style="width:72px;height:72px" alt="DPNHS Logo">
      <h3 class="fw-bold mb-1" style="color:#1e293b">Welcome</h3>
      <p class="text-muted" style="font-size:13.5px">Create Account</p>
    </div>

    <div class="mb-2">
      <label class="form-label fw-semibold" style="font-size:15px">First Name</label>
      <input type="text" 
            value="{{ old('first_name') }}" 
            class="form-control @error('first_name') is-invalid @enderror" 
            id="stu-fname" 
            placeholder="Enter your first name"
            name="first_name">
      @error('first_name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-2">
      <label class="form-label fw-semibold" style="font-size:15px">Last Name</label>
      <input  type="text" 
              value="{{ old('last_name') }}" 
              class="form-control @error('last_name') is-invalid @enderror"
              id="stu-lname" 
              placeholder="Enter your last name"
              name="last_name">
      @error('last_name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>


    <div class="mb-2">
      <label class="form-label fw-semibold" style="font-size:15px">Email</label>
      <input type="email" 
            value="{{ old('email') }}" 
            class="form-control @error('email') is-invalid @enderror" 
            id="stu-email"
            placeholder="Enter your email"
            name="email">
      @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold mb-0" style="font-size:15px">Password</label>
      <div class="position-relative mt-1">
        <input type="password"
        value="{{ old('password') }}"
        class="form-control pe-5 @error('password') is-invalid @enderror"
         id="stu-pw"
         placeholder="Enter your password"
         name="password"
         oninput="updatePasswordChecklist(this.value, 'stu-pw-req')">
        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-1 p-1 text-secondary border-0" onclick="togglePw('stu-pw',this)"><i class="bi bi-eye"></i></button>
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror

      </div>
      @include('partials.password-checklist', ['prefix' => 'stu-pw-req'])
    </div>

    <div class="mb-3">
      <label class="form-label fw-semibold mb-0" style="font-size:15px">Confirm Password</label>
      <div class="position-relative mt-1">
        <input type="password" value="{{ old('password_confirmation') }}"  class="form-control pe-5 @error('password_confirmation') is-invalid @enderror" id="stu-cpw" placeholder="Confirm your password" name="password_confirmation">
        <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-1 p-1 text-secondary border-0" onclick="togglePw('stu-cpw',this)"><i class="bi bi-eye"></i></button>

      
      </div>
    </div>

    <div class="form-check mb-3">
  <input class="form-check-input @error('terms_accepted') is-invalid @enderror"
         type="checkbox"
         id="terms_accepted"
         name="terms_accepted"
         value="1"
         {{ old('terms_accepted') ? 'checked' : '' }}>
  <label class="form-check-label" for="terms_accepted" style="font-size:13.5px">
    I have read and agree to the
    <a href="{{ asset('docs/terms_of_use.pdf') }}" target="_blank">Terms of Use</a>.
  </label>
  @error('terms_accepted')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

    <div class="g-recaptcha mb-3" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
    <button type="submit" class="btn btn-navy w-100 py-2 fw-semibold">Create Account</button>

  </div>
</div>
</form>
@endsection



@push('script')
{{-- Bootstrap JS and reCAPTCHA script already loaded once, globally, by layout/app.blade.php --}}
<script>

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