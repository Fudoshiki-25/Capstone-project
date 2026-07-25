{{--
  Enroll Step Modal
  ─────────────────
  A single Bootstrap modal that hosts all three enroll steps:
    • enroll-step-application-form
    • enroll-step-requirements
    • enroll-step-special-needs

  Usage: showEnrollStep('application-form') / showEnrollStep('requirements') / etc.
  The modal title and back button are updated dynamically by navigation.blade.php.
--}}
<div class="modal fade" id="enrollStepModal" tabindex="-1" aria-labelledby="enrollStepModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content" style="border-radius:16px;border:none">

      {{-- Modal Header --}}
      <div class="modal-header px-4 py-3" style="border-bottom:1px solid #e2e8f0">
        <div class="d-flex align-items-center gap-2">
          <div id="enrollStepModalIcon" style="width:34px;height:34px;border-radius:10px;background:#1a2a5e;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <i class="bi bi-pencil-fill text-white" style="font-size:14px"></i>
          </div>
          <div>
            <h6 class="modal-title mb-0 fw-bold" id="enrollStepModalLabel" style="color:#1e293b;font-size:15px">Enrollment Application Form</h6>
            <div class="text-muted" style="font-size:11.5px">SY 2026–2027 &nbsp;|&nbsp; Premiere Heights Learning Center, Inc.</div>
          </div>
        </div>
        <button type="button" class="btn-close ms-auto" onclick="hideEnrollStep()" aria-label="Close"></button>
      </div>

      {{-- Modal Body --}}
      <div class="modal-body px-4 py-4">
        @include('parent.panels.home.enrollment-application-form')
        @include('parent.panels.home.enrollment-requirements')
        @include('parent.panels.home.enrollment-special-needs')
      </div>

    </div>
  </div>
</div>