{{-- Shared nav buttons — included by both sidebar-mobile.blade.php and sidebar-desktop.blade.php --}}
<button onclick="showPanel('home')" class="sidebar-nav-btn active" data-panel="home">
  <i class="bi bi-house-fill me-2"></i> Home
</button>
{{-- My Children unlocks once a child's required documents are uploaded —
     earlier than admin approval (matches the panel's own content rule). --}}
@if($hasUnlockedChildProfile)
<button onclick="showPanel('my-children')" class="sidebar-nav-btn" data-panel="my-children">
  <i class="bi bi-people-fill me-2"></i> My Children (Profiles)
</button>
@else
<button class="sidebar-nav-btn locked-nav-btn" onclick="showLockedToast()" data-panel="my-children">
  <i class="bi bi-people-fill me-2"></i> My Children (Profiles)
  <i class="bi bi-lock-fill ms-auto" style="font-size:11px;opacity:.6"></i>
</button>
@endif

{{-- Tuition & Payments stays gated on actual admin approval, not just a
     complete document set — money shouldn't unlock before review. --}}
@if($hasEnrolledChildren)
<button onclick="showPanel('tuition-payments')" class="sidebar-nav-btn" data-panel="tuition-payments">
  <i class="bi bi-credit-card-fill me-2"></i> Tuition &amp; Payments
</button>
@else
<button class="sidebar-nav-btn locked-nav-btn" onclick="showLockedToast()" data-panel="tuition-payments">
  <i class="bi bi-credit-card-fill me-2"></i> Tuition &amp; Payments
  <i class="bi bi-lock-fill ms-auto" style="font-size:11px;opacity:.6"></i>
</button>
@endif
<button onclick="showPanel('my-profile')" class="sidebar-nav-btn" data-panel="my-profile">
  <i class="bi bi-person-gear me-2"></i> My Profile
</button>
