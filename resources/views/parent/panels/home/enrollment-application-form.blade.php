{{-- Enroll step: Application Form (the long registration form) --}}
{{-- Now rendered INLINE inside the "Enroll a Child" sub-panel, not a modal. --}}
<div id="enroll-step-application-form" class="enroll-step mb-4">

  {{-- Completed-state banner: shown instead of the form once Step 1 is saved.
       Clicking "Review / Edit" swaps back to the editable form (still saved
       to the same draft row — PUT, not a new record). --}}
  <div id="step1-complete-banner" class="card border rounded-3 p-3 mb-3 d-none" style="border-color:#16a34a;background:#f0fdf4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-check-circle-fill" style="font-size:20px;color:#16a34a"></i>
        <div>
          <div class="fw-bold" style="font-size:14px;color:#166534">Step 1 Complete — Application Saved</div>
          <div class="text-muted" style="font-size:12px" id="step1-complete-summary">—</div>
        </div>
      </div>
      <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleStep1Form(true)">
        <i class="bi bi-eye me-1"></i>Review / Edit
      </button>
    </div>
  </div>

  <div id="step1-form-wrapper">
  <div class="card border rounded-3 p-3 mb-3" style="background:linear-gradient(135deg,#b91c1c 0%,#1a2a5e 100%)">
    <div class="text-center text-white fw-bold mb-2" style="font-size:13px;text-transform:uppercase;letter-spacing:.8px">
      <i class="bi bi-mortarboard-fill me-2"></i>Premiere Heights Learning Center, Inc. (PHLCI)
    </div>
    <div class="text-center text-white-50 mb-3" style="font-size:12px">School Year 2026 – 2027 &nbsp;|&nbsp; Registration Form</div>
    <div class="d-flex justify-content-center gap-2">
      <button type="button" class="btn fw-semibold px-4 py-2" id="btnOldStudent" onclick="switchStudentType('old')"
        style="font-size:13px;background:#fff;color:#b91c1c;border:2px solid #fff;border-radius:30px">
        <i class="bi bi-person-check-fill me-1"></i> Old Student
      </button>
      <button type="button" class="btn fw-semibold px-4 py-2" id="btnNewStudent" onclick="switchStudentType('new')"
        style="font-size:13px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.5);border-radius:30px">
        <i class="bi bi-person-plus-fill me-1"></i> New Student
      </button>
    </div>
  </div>

  <div class="card border rounded-3 p-4 pb-3 mb-4">
    <div class="text-center pb-3 mb-3" style="border-bottom:2px solid #b91c1c">
      <div class="fw-bold" style="font-size:16px;color:#1e293b;letter-spacing:.3px">
        <span id="formTypeLabel">OLD STUDENT REGISTRATION FORM</span>
      </div>
      <div class="text-muted" style="font-size:12px">School Year 2026 – 2027 &nbsp;|&nbsp; Please fill out all required fields accurately</div>
    </div>

    <div class="fw-semibold mb-3" style="font-size:13px;color:#b91c1c;text-transform:uppercase;letter-spacing:.7px;border-bottom:1px solid #e2e8f0;padding-bottom:6px">
      Student's Information
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <label class="form-label fw-medium" style="font-size:12px">First Name <span class="text-danger">*</span></label>
        <input type="text" id="f_first_name" name="first_name" required class="form-control form-control-sm" placeholder="First Name">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-medium" style="font-size:12px">Middle Name <span class="text-muted fw-normal" style="font-size:11px">(optional)</span></label>
        <input type="text" id="f_middle_name" name="middle_name" class="form-control form-control-sm" placeholder="Leave blank if none">
        <div class="form-text mt-1" style="font-size:10.5px;color:#94a3b8">Will be saved as "N/A" if left blank</div>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-medium" style="font-size:12px">Last Name <span class="text-danger">*</span></label>
        <input type="text" id="f_last_name" name="last_name" required class="form-control form-control-sm" placeholder="Last Name">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-medium" style="font-size:12px">Suffix <span class="text-muted fw-normal" style="font-size:11px">(optional)</span></label>
        <input type="text" id="f_suffix" name="suffix" class="form-control form-control-sm" placeholder="Jr., III, IV…">
        <div class="form-text mt-1" style="font-size:10.5px;color:#94a3b8">Will be saved as "N/A" if left blank</div>
      </div>

      <div class="col-md-3">
        <label class="form-label fw-medium" style="font-size:12px">Student's LRN <span class="text-muted fw-normal" style="font-size:11px">(optional)</span></label>
        <input type="text" id="f_lrn" name="lrn" class="form-control form-control-sm font-monospace"
              placeholder="12-digit LRN" inputmode="numeric" pattern="\d{1,12}" maxlength="12"
              oninput="this.value=this.value.replace(/\D/g,'')">
        <div class="form-text mt-1" style="font-size:10.5px;color:#94a3b8">Numbers only · Will be "N/A" if left blank</div>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-medium" style="font-size:12px">Incoming Grade Level <span class="text-danger">*</span></label>
        <select id="f_grade_level" name="grade_level" required class="form-select form-select-sm">
          <option value="">Select grade level</option>
          <option>Kinder</option><option>Grade 1</option><option>Grade 2</option>
          <option>Grade 3</option><option>Grade 4</option><option>Grade 5</option>
          <option>Grade 6</option><option>Grade 7</option><option>Grade 8</option>
          <option>Grade 9</option><option>Grade 10</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-medium" style="font-size:12px">Date of Birth <span class="text-danger">*</span></label>
        <input type="date" id="f_birthday" name="birthday" required class="form-control form-control-sm" max="{{ date('Y-m-d') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label fw-medium" style="font-size:12px">Birth Place <span class="text-danger">*</span></label>
        <input type="text" id="f_birth_place" name="birth_place" required class="form-control form-control-sm" placeholder="City/Municipality">
      </div>

      <div class="col-12">
        <label class="form-label fw-medium" style="font-size:12px">Complete Address <span class="text-danger">*</span></label>
        <input type="text" id="f_address" name="address" required class="form-control form-control-sm"
              placeholder="House No., Street, Barangay, City/Municipality, Province">
      </div>

      <div class="col-md-6 PHLCI-new-only d-none">
        <label class="form-label fw-medium" style="font-size:12px">Last School Attended <span class="text-danger">*</span></label>
        <input type="text" id="f_last_school" name="last_school" class="form-control form-control-sm" placeholder="Name of last school attended">
      </div>

      <div class="col-md-6">
        <label class="form-label fw-medium" style="font-size:12px">Name of Mother <span class="text-danger">*</span></label>
        <input type="text" id="f_mother_name" name="mother_name" required class="form-control form-control-sm" placeholder="Full Name">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-medium" style="font-size:12px">Name of Father <span class="text-danger">*</span></label>
        <input type="text" id="f_father_name" name="father_name" required class="form-control form-control-sm" placeholder="Full Name">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-medium" style="font-size:12px">
          Guardian <span class="text-danger">*</span>
          <span class="text-muted fw-normal" style="font-size:11px">(name that will appear on the ID)</span>
        </label>
        <input type="text" id="f_guardian_name" name="guardian_name" required class="form-control form-control-sm" placeholder="Guardian's Full Name">
      </div>
      <div class="col-md-6">
        <label class="form-label fw-medium" style="font-size:12px">Emergency Contact Number <span class="text-danger">*</span></label>
        <input type="tel" id="f_emergency_contact" name="emergency_contact" required
              class="form-control form-control-sm" placeholder="09XXXXXXXXX"
              inputmode="numeric" pattern="^(09|\+639)\d{9}$" maxlength="11"
              oninput="this.value=this.value.replace(/[^\d+]/g,'')">
        <div class="form-text mt-1" style="font-size:10.5px;color:#94a3b8">Numbers only · e.g. 09171234567</div>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-medium d-block" style="font-size:12px">Preferred Class Session <span class="text-danger">*</span></label>
        <div class="d-flex gap-4 mt-1">
          <label style="font-size:13px;cursor:pointer"><input type="radio" name="classSession" id="f_session_am" value="AM" required> AM Session</label>
          <label style="font-size:13px;cursor:pointer"><input type="radio" name="classSession" id="f_session_pm" value="PM"> PM Session</label>
        </div>
      </div>
    </div>

    <!-- Tuition Payment Plan -->
    <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0">
      <div class="fw-semibold mb-1" style="font-size:13px;color:#1e293b"><i class="bi bi-calendar3-range me-2" style="color:#1a2a5e"></i>Tuition Payment Plan <span class="text-danger">*</span></div>
      <div class="text-muted mb-3" style="font-size:12px">Choose how you'd like to pay tuition for the school year. This determines your billing schedule once enrollment is finalized.</div>
      <div class="d-flex flex-wrap gap-2" id="paymentPlanCards">
        <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:150px;transition:all .15s" onclick="selectPaymentPlan(this,'monthly')">
          <input type="radio" name="paymentPlan" value="monthly" style="display:none">
          <span style="width:28px;height:28px;border-radius:8px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-calendar-month" style="font-size:13px;color:#0369a1"></i></span>
          <span>
            <span class="d-block" style="font-size:13px;font-weight:700;color:#1e293b">Monthly</span>
            <span class="d-block" style="font-size:11px;color:#64748b">10 installments</span>
          </span>
        </label>
        <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:150px;transition:all .15s" onclick="selectPaymentPlan(this,'quarterly')">
          <input type="radio" name="paymentPlan" value="quarterly" style="display:none">
          <span style="width:28px;height:28px;border-radius:8px;background:#f3e8ff;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-calendar3" style="font-size:13px;color:#7c3aed"></i></span>
          <span>
            <span class="d-block" style="font-size:13px;font-weight:700;color:#1e293b">Quarterly</span>
            <span class="d-block" style="font-size:11px;color:#64748b">4 installments</span>
          </span>
        </label>
      </div>
    </div>

    <!-- Proof of Payment -->
    <div class="p-3 rounded-3 mb-3" style="background:#f8fafc;border:1px solid #e2e8f0">
      <div class="fw-semibold mb-1" style="font-size:13px;color:#1e293b"><i class="bi bi-receipt me-2" style="color:#16a34a"></i>Proof of Payment *</div>
      <div class="text-muted mb-3" style="font-size:12px">Choose how you paid, fill in the reference number, then upload your screenshot or receipt.</div>
      <div class="mb-3">
        <div class="fw-semibold mb-2" style="font-size:12px;color:#374151;text-transform:uppercase;letter-spacing:.05em">Step 1 — Payment Method</div>
        <div class="d-flex flex-wrap gap-2" id="payMethodCards">
          <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:110px;transition:all .15s" onclick="selectPayMethod(this,'GCash')">
            <input type="radio" name="payMethod" value="GCash" style="display:none">
            <span style="width:28px;height:28px;border-radius:8px;background:#f3e8ff;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-phone-fill" style="font-size:13px;color:#7c3aed"></i></span>
            <span style="font-size:13px;font-weight:700;color:#1e293b">GCash</span>
          </label>
          <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:110px;transition:all .15s" onclick="selectPayMethod(this,'Maya')">
            <input type="radio" name="payMethod" value="Maya" style="display:none">
            <span style="width:28px;height:28px;border-radius:8px;background:#e0f2fe;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-wallet2" style="font-size:13px;color:#0369a1"></i></span>
            <span style="font-size:13px;font-weight:700;color:#1e293b">Maya</span>
          </label>
          <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:130px;transition:all .15s" onclick="selectPayMethod(this,'Bank Transfer')">
            <input type="radio" name="payMethod" value="Bank Transfer" style="display:none">
            <span style="width:28px;height:28px;border-radius:8px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-bank" style="font-size:13px;color:#1d4ed8"></i></span>
            <span style="font-size:13px;font-weight:700;color:#1e293b">Bank Transfer</span>
          </label>
          <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:100px;transition:all .15s" onclick="selectPayMethod(this,'Cash')">
            <input type="radio" name="payMethod" value="Cash" style="display:none">
            <span style="width:28px;height:28px;border-radius:8px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0"><i class="bi bi-cash" style="font-size:13px;color:#16a34a"></i></span>
            <span style="font-size:13px;font-weight:700;color:#1e293b">Cash</span>
          </label>
        </div>
      </div>
      <div id="proofUploadBlock" class="mb-2">
        <div class="fw-semibold mb-1" style="font-size:12px;color:#374151;text-transform:uppercase;letter-spacing:.05em" id="proofUploadLabel">Step 2 — Upload Proof</div>
        <label class="d-flex align-items-center gap-2 p-3 rounded-3 border" style="cursor:pointer;background:#fff;border-style:dashed!important">
          <i class="bi bi-cloud-arrow-up" style="font-size:22px;color:#94a3b8;flex-shrink:0"></i>
          <div>
            <div style="font-size:13px;font-weight:600;color:#374151">Click to upload screenshot or receipt</div>
            <div style="font-size:11px;color:#94a3b8">JPG, PNG or PDF &middot; max 5MB</div>
          </div>
          <input type="file" accept="image/*,.pdf" style="display:none" onchange="showPaymentFileName(this)">
        </label>
        <span id="paymentFileName" class="d-block mt-2 text-muted" style="font-size:12px"></span>
        <div id="payProofPreview" style="display:none;margin-top:8px;max-width:260px;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fff;padding:6px">
          <img id="payProofImg" src="" alt="" style="max-width:100%;max-height:120px;object-fit:contain;border-radius:6px">
        </div>
      </div>
      <div class="mt-3 p-2 rounded-2" style="background:#eff6ff;border:1px solid #bfdbfe;font-size:12px;color:#1e40af">
        <i class="bi bi-info-circle-fill me-1"></i>
        After submitting, the admin will review your proof and send an <strong>acknowledgment receipt</strong> to confirm payment was received.
      </div>
    </div>

    <div class="p-3 rounded-2 mb-3" style="background:#fffbeb;border:1px solid #fcd34d">
      <div class="fw-semibold mb-1" style="font-size:13px;color:#92400e"><i class="bi bi-bell-fill me-2"></i>Please be reminded</div>
      <div style="font-size:12.5px;color:#78350f;line-height:1.7">
        Wait for the confirmation and schedule of books and uniform distribution.<br>
        <strong>School and P.E. Uniform fitting</strong> will be on <strong>May 8, 15 and 22, 2026</strong>.
      </div>
    </div>

    <div class="row g-3 mt-2 pt-3" style="border-top:1px solid #e2e8f0">
      <div class="col-md-3 ms-auto">
        <button class="btn btn-outline-secondary w-100 py-2 fw-semibold" onclick="cancelEnrollment()">Cancel</button>
      </div>
      <div class="col-md-4">
        <button class="btn w-100 py-2 fw-semibold" style="background:#b91c1c;color:#fff" onclick="submitPHLCIForm()">
          <i class="bi bi-send-fill me-1"></i> Submit Registration
        </button>
      </div>
    </div>
  </div>
  </div>{{-- /step1-form-wrapper --}}
</div>