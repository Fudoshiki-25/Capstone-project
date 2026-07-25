  <div class="bg-white border-end p-3 flex-shrink-0 d-none d-lg-flex flex-column" style="width:270px;min-height:100%">
    <div class="fw-bold mb-3 text-center" style="font-size:14px;color:#1e293b">Parent Portal</div>
  <div id="sidebarAvatar" class="stu-profile-avatar" style="width:80px;height:80px;margin:0 auto 10px auto;background:#64748b">
    @if($user->profile_pic)
        <img src="{{ asset('storage/' . $user->profile_pic) }}"
             style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Profile">
    @else
        {{ $initials }}
    @endif
</div>
    <p class="text-center fw-semibold mb-0" style="font-size:13px;color:#1e293b">{{ $fullName }}</p>
    <p class="text-center text-muted mb-3" style="font-size:12px">{{ $user->email }}</p>
    <div class="fw-bold mt-2 mb-2" style="font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:.6px">Menu</div>
    <nav class="d-flex flex-column gap-1" id="desktopNav">
      @include('parent.partials.sidebar-nav-links')
    </nav>
    <div class="mt-auto pt-3 border-top">
      <a href="{{ route('logout') }}" class="sb-logout">
        <i class="bi bi-box-arrow-left"></i><span>Logout</span>
      </a>
    </div>
  </div>
