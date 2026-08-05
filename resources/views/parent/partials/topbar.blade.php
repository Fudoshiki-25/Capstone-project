<div class="bg-white border-bottom d-flex align-items-center justify-content-between px-4 py-2 sticky-top" style="z-index:100">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm d-lg-none me-1 p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#studentSidebar" aria-controls="studentSidebar">
      <i class="bi bi-list fs-5"></i>
    </button>
    <img src="{{ asset('photo/logo.png') }}" class="brand-logo" alt="PHLCI Logo" style="width:55px;height:55px;">
    <div>
      <div class="fw-bold text-navy" style="font-size:15px;line-height:1.2">PHLCI</div>
      <div class="text-muted" style="font-size:11px">Parent Portal</div>
    </div>
  </div>
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm text-secondary" id="pushNotifyBtn" type="button" title="Enable notifications" onclick="togglePushSubscription()">
      <i class="bi bi-bell" id="pushNotifyIcon" style="font-size:18px"></i>
    </button>
    <div id="topbarAvatar" class="brand-logo" style="background:#64748b"> @if($user->profile_pic)
        <img src="{{ asset('storage/' . $user->profile_pic) }}"
             style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Profile">
    @else
        {{ $initials }}
    @endif</div>
    <div class="d-none d-md-block">
      <div class="fw-semibold" style="font-size:14px;color:#1e293b">{{ $fullName }}</div>
      <div class="text-muted" style="font-size:12px">{{ $user->email }}</div>
    </div>
  </div>
</div>
