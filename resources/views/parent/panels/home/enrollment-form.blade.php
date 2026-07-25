{{-- Sub-panel: Enrollment Form (slides in over home-main-view) --}}
<div id="home-sub-enrollment-form" class="home-sub-panel d-none" style="position:relative">
  <div class="d-flex align-items-center gap-2 mb-3">
    <button class="btn btn-sm btn-outline-secondary" onclick="cancelEnrollment()"><i class="bi bi-arrow-left"></i> Back</button>
    <h5 class="mb-0 fw-bold" style="color:#1e293b">Enroll a Child</h5>
  </div>
  <div class="text-muted mb-4" style="font-size:14px">SY 2026–2027 &nbsp;|&nbsp; Complete the steps below</div>

  <div style="max-width:100%">
    @include('parent.panels.home.enrollment-application-form')
    @include('parent.panels.home.enrollment-requirements')
  </div>

  {{-- Fixed "Enroll Now" button — always visible while in this flow, not
       nested inside either step. Becomes enabled once both required
       documents (Report Card + PSA Birth Certificate) are uploaded. --}}
  <div id="enrollNowFixedBtn" class="d-none" style="position:fixed;bottom:24px;right:24px;z-index:1040">
    <button type="button" class="btn fw-bold px-4 py-3 shadow" id="enrollNowBtn" disabled
      style="background:#16a34a;color:#fff;border-radius:14px;font-size:14px;opacity:.5"
      onclick="showEnrollConfirmModal()">
      <i class="bi bi-check-circle-fill me-2"></i>Enroll Now
    </button>
    <div id="enrollNowHint" class="text-end mt-1" style="font-size:11px;color:#92400e"></div>
  </div>
</div>{{-- /home-sub-enrollment-form --}}

@include('parent.panels.home.enroll-confirm-modal')