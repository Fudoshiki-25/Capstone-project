<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="studentSidebar" style="width:270px">
    <div class="offcanvas-header border-bottom">
      <div class="fw-bold" style="font-size:14px;color:#1e293b">Parent Portal</div>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3 d-flex flex-column">
      <div class="stu-profile-avatar mx-auto mb-1" style="width:80px;height:80px;background:#64748b">
        @if($user->profile_pic)
            <img src="{{ asset('storage/' . $user->profile_pic) }}"
                 style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Profile">
        @else
            {{ $initials }}
        @endif
      </div>
      <p class="text-center fw-semibold mb-0" style="font-size:13px;color:#1e293b">{{ $fullName }}</p>
      <p class="text-center text-muted mb-4" style="font-size:11px">{{ $user->email }}</p>
      <div class="fw-bold mt-2 mb-2" style="font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.6px">Menu</div>
      <nav class="d-flex flex-column gap-1" id="mobileNav">
        @include('parent.partials.sidebar-nav-links')
      </nav>
      <div class="mt-auto pt-3 border-top">
        <a href="{{ route('logout') }}" class="sb-logout">
          <i class="bi bi-box-arrow-left"></i><span>Logout</span>
        </a>
      </div>
    </div>
  </div>