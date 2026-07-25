{{-- Enroll step: Submit Requirements (merged — PSA, Report Card, Good Moral, Medical Cert) --}}
{{-- Hidden until Step 1 is saved (toggled via JS once a draft enrollment_id exists). --}}
<div id="enroll-step-requirements" class="enroll-step d-none mb-4">
  <div class="card border rounded-3 p-4 mb-3">
    <div class="d-flex align-items-center gap-2 mb-1">
      <div style="width:28px;height:28px;border-radius:8px;background:#c0392b;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i class="bi bi-upload text-white" style="font-size:13px"></i>
      </div>
      <p class="fw-bold mb-0" style="font-size:15px;color:#1e293b">Step 2 — Submit Requirements</p>
    </div>
    <p class="text-muted mb-3" style="font-size:12px">Upload each document below. Accepted formats: PDF, JPG, PNG (max 5 MB each). Report Card and PSA Birth Certificate are required; the rest are optional.</p>
    <div class="d-flex flex-column gap-3" id="req-doc-list"></div>
    <div id="req-loading" class="text-center py-4 text-muted d-none" style="font-size:13px">
      <div class="spinner-border spinner-border-sm me-2"></div> Loading your uploads…
    </div>
  </div>
  <div class="d-flex align-items-center gap-2 p-3 rounded-2 mb-3" style="background:#f0fdf4;border:1px solid #bbf7d0;font-size:12.5px;color:#166534">
    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
    <span>Each document saves immediately when uploaded. You can re-upload anytime to replace a file.</span>
  </div>
</div>