{{-- Enrollment application form: student type toggle, payment method, session cards, submit/reset --}}
<script>
// ── Student type toggle ────────────────────────────────────────────────────
var PHLCIStudentType = 'old';
// null = creating a new child. Set to an enrollment id when editing an
// existing one via the "Edit" button on a student card.
var editingEnrollmentId = null;

function switchStudentType(type) {
  PHLCIStudentType = type;
  var newOnlyEls = document.querySelectorAll('.PHLCI-new-only');
  var btnOld = document.getElementById('btnOldStudent');
  var btnNew = document.getElementById('btnNewStudent');
  var label  = document.getElementById('formTypeLabel');
  if (type === 'new') {
    newOnlyEls.forEach(el => el.classList.remove('d-none'));
    btnNew.style.cssText = 'font-size:13px;background:#fff;color:#1a2a5e;border:2px solid #fff;border-radius:30px';
    btnOld.style.cssText = 'font-size:13px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.5);border-radius:30px';
    if (label) label.textContent = 'NEW STUDENT REGISTRATION FORM';
  } else {
    newOnlyEls.forEach(el => el.classList.add('d-none'));
    btnOld.style.cssText = 'font-size:13px;background:#fff;color:#b91c1c;border:2px solid #fff;border-radius:30px';
    btnNew.style.cssText = 'font-size:13px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.5);border-radius:30px';
    if (label) label.textContent = 'OLD STUDENT REGISTRATION FORM';
  }
}

// ── Payment method ─────────────────────────────────────────────────────────
function selectPayMethod(card, method) {
  document.querySelectorAll('.pay-method-card').forEach(c => { c.style.background='#fff'; c.style.borderColor='#e2e8f0'; });
  card.style.background='#f0f9ff'; card.style.borderColor='#38bdf8';
  var uploadStep = document.getElementById('proofUploadLabel');
  if (method === 'Cash') { if (uploadStep) uploadStep.textContent = 'Step 2 — Upload Official Receipt or OR Number Photo'; }
  else { if (uploadStep) uploadStep.textContent = 'Step 2 — Upload Screenshot / Receipt'; }
}

function showPaymentFileName(input) {
  var el = document.getElementById('paymentFileName');
  if (el && input.files.length) {
    el.textContent = '\u2714 ' + input.files[0].name;
    var file = input.files[0];
    var preview = document.getElementById('payProofPreview');
    var img = document.getElementById('payProofImg');
    if (preview && img && file.type.startsWith('image/')) {
      var reader = new FileReader();
      reader.onload = e => { img.src = e.target.result; preview.style.display = 'block'; };
      reader.readAsDataURL(file);
    }
  }
}

// NOTE: getCsrfToken() now lives in navigation.blade.php (shared helper).

// ── Edit an existing enrollment ─────────────────────────────────────────────
// Called from the "Edit" button on a student card (main-view.blade.php).
// Fetches the saved Step 1 data via GET /enrollment/{id}, pre-fills every
// field, then opens the same application-form modal used for new children.
// submitPHLCIForm() checks editingEnrollmentId to decide POST vs PUT.
function openEditEnrollment(enrollmentId) {
  fetch('/enrollment/' + enrollmentId, {
    headers: { 'Accept': 'application/json' },
  })
  .then(res => {
    if (!res.ok) { throw new Error('Failed to load enrollment data.'); }
    return res.json();
  })
  .then(data => {
    var e = data.enrollment;
    editingEnrollmentId = e.id;

    // Determine student type from whether last_school has a value (new-only field)
    switchStudentType(e.last_school ? 'new' : 'old');

    document.getElementById('f_first_name').value        = e.first_name || '';
    document.getElementById('f_middle_name').value        = e.middle_name || '';
    document.getElementById('f_last_name').value          = e.last_name || '';
    document.getElementById('f_suffix').value             = e.suffix || '';
    document.getElementById('f_lrn').value                = e.lrn || '';
    document.getElementById('f_grade_level').value         = e.grade_level || '';
    document.getElementById('f_birthday').value            = e.birthday || '';
    document.getElementById('f_birth_place').value         = e.birth_place || '';
    document.getElementById('f_address').value             = e.address || '';
    var lastSchoolEl = document.getElementById('f_last_school');
    if (lastSchoolEl) lastSchoolEl.value = e.last_school || '';
    document.getElementById('f_mother_name').value         = e.mother_name || '';
    document.getElementById('f_father_name').value         = e.father_name || '';
    document.getElementById('f_guardian_name').value       = e.guardian_name || '';
    document.getElementById('f_emergency_contact').value   = e.emergency_contact || '';

    document.querySelectorAll('input[name="classSession"]').forEach(r => {
      r.checked = (r.value === e.classSession);
    });

    document.querySelectorAll('.pay-method-card').forEach(card => {
      var radio = card.querySelector('input[name="payMethod"]');
      if (radio && radio.value === e.payMethod) {
        selectPayMethod(card, e.payMethod);
      }
    });

    // Proof of payment file input is intentionally left empty — browsers
    // can't pre-fill file inputs. The parent must reselect a file; the
    // "Submit Registration" button label changes below to make this clear.
    var proofInput = document.querySelector('#proofUploadBlock input[type="file"]');
    if (proofInput) proofInput.value = '';
    var fn = document.getElementById('paymentFileName');
    if (fn) fn.textContent = 'Please re-upload your proof of payment to save changes.';
    var preview = document.getElementById('payProofPreview');
    if (preview) preview.style.display = 'none';

    // Swap the submit button's label/icon to make edit mode obvious
    var submitBtn = document.querySelector('[onclick="submitPHLCIForm()"]');
    if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-save-fill me-1"></i> Save Changes';

    showEnrollStep('application-form');
  })
  .catch(err => {
    console.error('Failed to load enrollment for editing:', err);
    showToast('danger', 'Could not load this enrollment for editing. Please try again.');
  });
}

// ── Submit form ────────────────────────────────────────────────────────────
function submitPHLCIForm() {
  var required = [
    {id:'f_first_name',label:'First Name'},{id:'f_last_name',label:'Last Name'},
    {id:'f_grade_level',label:'Incoming Grade Level'},{id:'f_birthday',label:'Date of Birth'},
    {id:'f_birth_place',label:'Birth Place'},{id:'f_address',label:'Complete Address'},
    {id:'f_mother_name',label:'Name of Mother'},{id:'f_father_name',label:'Name of Father'},
    {id:'f_guardian_name',label:'Guardian Name'},{id:'f_emergency_contact',label:'Emergency Contact'},
  ];
  var errors = [], firstErrorEl = null;
  required.forEach(f => {
    var el = document.getElementById(f.id);
    if (!el) return;
    if (!el.value.trim()) { el.classList.add('is-invalid'); errors.push(f.label); if (!firstErrorEl) firstErrorEl = el; }
    else { el.classList.remove('is-invalid'); }
  });
  var session = document.querySelector('input[name="classSession"]:checked');
  if (!session) { errors.push('Preferred Class Session'); document.querySelectorAll('input[name="classSession"]').forEach(r => r.closest('label').style.color = '#dc2626'); }
  else { document.querySelectorAll('input[name="classSession"]').forEach(r => r.closest('label').style.color = ''); }
  var lastSchoolEl = document.getElementById('f_last_school');
  if (lastSchoolEl && !lastSchoolEl.closest('.PHLCI-new-only').classList.contains('d-none') && !lastSchoolEl.value.trim()) {
    lastSchoolEl.classList.add('is-invalid'); errors.push('Last School Attended'); if (!firstErrorEl) firstErrorEl = lastSchoolEl;
  } else if (lastSchoolEl) { lastSchoolEl.classList.remove('is-invalid'); }
  var lrnEl = document.getElementById('f_lrn');
  if (lrnEl && lrnEl.value.trim() && !/^\d+$/.test(lrnEl.value.trim())) {
    lrnEl.classList.add('is-invalid'); errors.push('LRN (numbers only)'); if (!firstErrorEl) firstErrorEl = lrnEl;
  } else if (lrnEl) { lrnEl.classList.remove('is-invalid'); }
  var contactEl = document.getElementById('f_emergency_contact');
  if (contactEl && contactEl.value.trim() && !/^(09|\+639)\d{9}$/.test(contactEl.value.trim())) {
    contactEl.classList.add('is-invalid'); errors.push('Emergency Contact (must be e.g. 09171234567)'); if (!firstErrorEl) firstErrorEl = contactEl;
  }
  var method = document.querySelector('input[name="payMethod"]:checked');
  if (!method) { errors.push('Payment Method'); }
  var proofInput = document.querySelector('#proofUploadBlock input[type="file"]');
  if (!proofInput || !proofInput.files.length) { errors.push('Proof of Payment'); }

  if (errors.length > 0) {
    // Scroll inside the modal body to the first error field
    if (firstErrorEl) {
      firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstErrorEl.focus();
    }
    showToast('danger', 'Please fill in: <strong>' + errors[0] + '</strong>' + (errors.length > 1 ? ' and ' + (errors.length - 1) + ' other field(s).' : '.'));
    return;
  }

  ['f_middle_name','f_suffix','f_lrn'].forEach(id => { var el = document.getElementById(id); if (el && !el.value.trim()) el.value = 'N/A'; });

  var firstName  = document.getElementById('f_first_name').value.trim();
  var lastName   = document.getElementById('f_last_name').value.trim();
  var grade      = document.getElementById('f_grade_level').value;
  var sessionVal = session ? session.value : '';
  var payMethod  = method ? method.value : '';

  // Build FormData (needed because we're sending a file)
  var formData = new FormData();
  formData.append('first_name', firstName);
  formData.append('middle_name', document.getElementById('f_middle_name').value.trim());
  formData.append('last_name', lastName);
  formData.append('suffix', document.getElementById('f_suffix').value.trim());
  formData.append('lrn', document.getElementById('f_lrn').value.trim());
  formData.append('grade_level', grade);
  formData.append('birthday', document.getElementById('f_birthday').value);
  formData.append('birth_place', document.getElementById('f_birth_place').value.trim());
  formData.append('address', document.getElementById('f_address').value.trim());
  if (lastSchoolEl && !lastSchoolEl.closest('.PHLCI-new-only').classList.contains('d-none')) {
    formData.append('last_school', lastSchoolEl.value.trim());
  }
  formData.append('mother_name', document.getElementById('f_mother_name').value.trim());
  formData.append('father_name', document.getElementById('f_father_name').value.trim());
  formData.append('guardian_name', document.getElementById('f_guardian_name').value.trim());
  formData.append('emergency_contact', document.getElementById('f_emergency_contact').value.trim());
  formData.append('classSession', sessionVal);
  formData.append('payMethod', payMethod);
  formData.append('proof_of_payment', proofInput.files[0]);

  // Disable submit button while saving to avoid double-submits
  var submitBtn = document.querySelector('[onclick="submitPHLCIForm()"]');
  var isEditing = !!editingEnrollmentId;
  if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> ' + (isEditing ? 'Saving…' : 'Submitting…'); }

  var url = isEditing ? ('/enrollment/' + editingEnrollmentId) : '{{ route("enrollment.store") }}';
  // Always POST — file uploads (multipart/form-data) aren't reliably sent via
  // a real PUT request in all server/proxy setups. For edits, we POST with a
  // _method=PUT field instead; Laravel's middleware treats this as a real PUT
  // (standard Laravel "method spoofing" pattern, not a workaround/hack).
  if (isEditing) { formData.append('_method', 'PUT'); }

  fetch(url, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
    body: formData,
  })
  .then(res => {
    if (!res.ok) {
      return res.json().then(data => { throw data; });
    }
    return res.json();
  })
  .then(data => {
    var saved = data.enrollment;

    // The real card for this child now lives in the DB and will render
    // automatically via main-view.blade.php ($enrolledChildren). Reload so
    // the parent sees it immediately, instead of faking a card client-side.
    showToast('success', isEditing
      ? ('<strong>' + saved.name + '</strong> updated successfully.')
      : ('<strong>' + saved.name + '</strong> registered! Waiting for admin review.'));
    setTimeout(function() { window.location.reload(); }, 900); // brief pause so the toast is visible before reload
  })
  .catch(err => {
    console.error('Enrollment submit failed:', err);
    var msg = (err && err.message) ? err.message : 'Something went wrong while submitting. Please try again.';
    if (err && err.errors) {
      // Laravel validation error format: { message, errors: { field: [msgs] } }
      var firstField = Object.keys(err.errors)[0];
      msg = err.errors[firstField][0];
    }
    showToast('danger', msg);
  })
  .finally(() => {
    if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Submit Registration'; }
  });
}

// NOTE: addSessionCard() / removeSessionCard() were removed. Student cards
// are now rendered server-side from $enrolledChildren in main-view.blade.php,
// so there's no need to build a fake card in JS after submit — the page
// reload above shows the real, persisted record instead.

function resetPHLCIForm() {
  editingEnrollmentId = null;
  ['f_first_name','f_middle_name','f_last_name','f_suffix','f_lrn','f_grade_level','f_birthday',
   'f_birth_place','f_address','f_last_school','f_mother_name','f_father_name','f_guardian_name','f_emergency_contact']
    .forEach(id => { var el = document.getElementById(id); if (el) { el.value=''; el.classList.remove('is-invalid'); } });
  document.querySelectorAll('input[name="classSession"]').forEach(r => r.checked = false);
  document.querySelectorAll('.pay-method-card').forEach(c => { c.style.background='#fff'; c.style.borderColor='#e2e8f0'; });
  var proofInput = document.querySelector('#proofUploadBlock input[type="file"]');
  if (proofInput) proofInput.value = '';
  var fn = document.getElementById('paymentFileName');
  if (fn) fn.textContent = '';
  var preview = document.getElementById('payProofPreview');
  if (preview) preview.style.display = 'none';
  var submitBtn = document.querySelector('[onclick="submitPHLCIForm()"]');
  if (submitBtn) submitBtn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Submit Registration';
  switchStudentType('old');
}
</script>