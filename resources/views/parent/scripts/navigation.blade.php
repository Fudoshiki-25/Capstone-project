{{-- Panel & sub-panel navigation: showPanel, home sub-panels, enroll flow, child/tuition tab switching --}}
<script>
// ── CSRF helper ────────────────────────────────────────────────────────────
// Shared by every script that makes a fetch() POST (enrollment form,
// requirements upload, finalize). Defined here since navigation.blade.php
// is the foundational script every other enroll-related script depends on.
// Assumes the standard Laravel Blade layout has:
//   <meta name="csrf-token" content="{{ csrf_token() }}">
// in <head>. Update the selector below if your layout names it differently.
function getCsrfToken() {
  var meta = document.querySelector('meta[name="csrf-token"]');
  return meta ? meta.getAttribute('content') : '';
}

// ── Panel navigation ───────────────────────────────────────────────────────
function showPanel(panelId) {
  document.querySelectorAll('.panel-section').forEach(p => p.classList.add('d-none'));
  document.getElementById('panel-' + panelId).classList.remove('d-none');
  if (panelId !== 'home') { hideHomeSubPanel(); }
  document.querySelectorAll('.sidebar-nav-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.panel === panelId);
  });
  const oc = document.getElementById('studentSidebar');
  if (oc && bootstrap && bootstrap.Offcanvas.getInstance(oc)) {
    bootstrap.Offcanvas.getInstance(oc).hide();
  }
  if (panelId === 'tuition-payments') {
    var visiblePane = document.querySelector('.child-tuition-pane:not(.d-none)');
    if (visiblePane) loadTuitionData(visiblePane.id.replace('tuition-pane-', ''));
    loadTuitionHistory();
  }
}

// ── Home sub-panel (enrollment flow) ──────────────────────────────────────
function showHomeSubPanel(subId) {
  document.getElementById('home-main-view').classList.add('d-none');
  document.querySelectorAll('.home-sub-panel').forEach(p => p.classList.add('d-none'));
  var el = document.getElementById('home-sub-' + subId);
  if (el) el.classList.remove('d-none');
}

function hideHomeSubPanel() {
  document.querySelectorAll('.home-sub-panel').forEach(p => p.classList.add('d-none'));
  document.getElementById('home-main-view').classList.remove('d-none');
}

// ── Cancel out of the enroll flow entirely ─────────────────────────────────
// Used by the "Back"/"Cancel" buttons. Just navigates back to Home — does
// NOT delete the draft (if one exists), so the parent can resume later via
// the "Continue Enrollment" banner.
function cancelEnrollment() {
  hideHomeSubPanel();
}

// ── Step 1 ⇄ completed-banner toggle ───────────────────────────────────────
// After Step 1 is saved, the form is replaced by a green "complete" banner.
// Clicking "Review / Edit" on that banner calls this with show=true to swap
// back to the editable form (still the same draft row — PUT, not a new one).
function toggleStep1Form(showForm) {
  var formWrapper = document.getElementById('step1-form-wrapper');
  var banner      = document.getElementById('step1-complete-banner');
  if (!formWrapper || !banner) return;
  if (showForm) {
    formWrapper.classList.remove('d-none');
    banner.classList.add('d-none');
  } else {
    formWrapper.classList.add('d-none');
    banner.classList.remove('d-none');
  }
}

// ── Open child profile from dashboard card ─────────────────────────────────
// Switches to the "My Children" panel and selects the pane for this specific
// child, looked up by their real student_enrollment.id (not array position).
function openChildProfile(childId) {
  showPanel('my-children');
  switchChildProfileTab(childId);
}

// ── Resume an unfinished draft ──────────────────────────────────────────────
// Called from the "Continue Enrollment" banner on Home (see main-view.blade.php).
// Opens the enroll flow and loads that draft's saved Step 1 data + reveals
// Step 2, picking up right where the parent left off.
function resumeDraftEnrollment(enrollmentId) {
  showHomeSubPanel('enrollment-form');
  loadDraftIntoForm(enrollmentId); // defined in enrollment-application-form-script.blade.php
}

// ── Delete an enrollment (draft or pending) ─────────────────────────────────
// Only shown/allowed while status is 'draft' or 'pending' (enforced
// server-side too — the button itself is hidden in the blade once status
// changes, but the backend re-checks in case of stale page state).
function deleteEnrollment(childId, childName) {
  if (!confirm('Delete the enrollment for "' + childName + '"? This cannot be undone — all uploaded documents and proof of payment will be permanently removed.')) {
    return;
  }

  fetch('/enrollment/' + childId, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
  })
  .then(res => res.json().then(data => ({ ok: res.ok, data })))
  .then(({ ok, data }) => {
    if (ok) {
      showToast('success', data.message || 'Enrollment deleted.');
      setTimeout(function() { window.location.reload(); }, 700);
    } else {
      showToast('danger', data.message || 'Unable to delete this enrollment.');
    }
  })
  .catch(() => showToast('danger', 'Something went wrong. Please try again.'));
}

// ── Child profile tab switcher ─────────────────────────────────────────────
// Panes and tab buttons are rendered with data-child-id (see
// panel-my-children.blade.php). We look up by that attribute instead of
// array index so this works correctly no matter how many children exist
// or what order they're rendered in.
function switchChildProfileTab(childId) {
  document.querySelectorAll('.child-profile-pane').forEach(p => p.classList.add('d-none'));
  var pane = document.querySelector('.child-profile-pane[data-child-id="' + childId + '"]');
  if (pane) pane.classList.remove('d-none');

  document.querySelectorAll('[id^="child-profile-tab-"]').forEach(btn => {
    btn.style.background = '#f1f5f9'; btn.style.color = '#374151';
  });
  var tab = document.querySelector('.child-profile-tab[data-child-id="' + childId + '"]');
  if (tab) { tab.style.background = '#1a2a5e'; tab.style.color = '#fff'; }
}

// ── Tuition child switch ───────────────────────────────────────────────────
function switchTuitionChild(idx) {
  document.querySelectorAll('.child-tuition-pane').forEach(p => p.classList.add('d-none'));
  var pane = document.getElementById('tuition-pane-' + idx);
  if (pane) pane.classList.remove('d-none');
  loadTuitionData(idx);
}

var _tuitionLoaded = {};
var STATUS_BADGES = {
  paid:    '<span class="badge px-3 py-1 rounded-pill fw-semibold" style="background:#dcfce7;color:#166534;font-size:11px"><i class="bi bi-check-circle-fill me-1"></i>Paid</span>',
  pending: '<span class="badge px-3 py-1 rounded-pill fw-semibold" style="background:#e0f2fe;color:#0369a1;font-size:11px"><i class="bi bi-hourglass-split me-1"></i>Pending Verification</span>',
  unpaid:  '<span class="badge px-3 py-1 rounded-pill fw-semibold" style="background:#fef3c7;color:#b45309;font-size:11px"><i class="bi bi-clock me-1"></i>Unpaid</span>',
};

function loadTuitionData(idx, force) {
  if (_tuitionLoaded[idx] && !force) return;
  var pane = document.getElementById('tuition-pane-' + idx);
  if (!pane) return;
  var enrollmentId = pane.dataset.enrollmentId;
  var loadingEl = document.getElementById('tuition-loading-' + idx);
  var contentEl = document.getElementById('tuition-content-' + idx);

  fetch('{{ route("tuition.show") }}?enrollment_id=' + encodeURIComponent(enrollmentId), {
    headers: { 'Accept': 'application/json' },
  })
  .then(res => res.json())
  .then(data => {
    _tuitionLoaded[idx] = true;
    if (loadingEl) loadingEl.classList.add('d-none');
    if (contentEl) { contentEl.classList.remove('d-none'); contentEl.innerHTML = renderTuitionContent(data, idx); }
  })
  .catch(() => {
    if (loadingEl) loadingEl.innerHTML = '<span class="text-danger">Could not load billing information. Please try again.</span>';
  });
}

function renderTuitionContent(data, idx) {
  if (!data.plan) {
    return '<div class="p-4 rounded-3 text-center text-muted" style="background:#f8fafc;border:1px solid #e2e8f0;font-size:13px">' +
      'No tuition plan yet — this is generated automatically once enrollment is finalized.</div>';
  }

  var total = data.plan.total_amount;
  var downPayment = data.plan.down_payment || 0;
  var paid  = downPayment + data.payments.filter(p => p.status === 'paid').reduce((sum, p) => sum + p.amount_due, 0);
  var balance = total - paid;
  var peso = n => '₱' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  var html = '<div class="row g-3 mb-4">' +
    '<div class="col-sm-4"><div class="card border rounded-3 p-3 text-center h-100">' +
      '<div class="text-muted mb-1" style="font-size:12px">Total Tuition</div>' +
      '<div class="fw-bold" style="font-size:20px;color:#1e293b">' + peso(total) + '</div>' +
      '<div class="text-muted" style="font-size:11px">' + (data.plan.plan_type === 'monthly' ? 'Monthly Plan' : 'Quarterly Plan') + '</div>' +
    '</div></div>' +
    '<div class="col-sm-4"><div class="card border rounded-3 p-3 text-center h-100" style="border-color:#bbf7d0!important;background:#f0fdf4">' +
      '<div class="text-muted mb-1" style="font-size:12px">Total Paid</div>' +
      '<div class="fw-bold" style="font-size:20px;color:#16a34a">' + peso(paid) + '</div>' +
      '<div class="text-muted" style="font-size:11px">Verified payments</div>' +
    '</div></div>' +
    '<div class="col-sm-4"><div class="card border rounded-3 p-3 text-center h-100" style="border-color:#fed7aa!important;background:#fff7ed">' +
      '<div class="text-muted mb-1" style="font-size:12px">Remaining Balance</div>' +
      '<div class="fw-bold" style="font-size:20px;color:#d97706">' + peso(balance) + '</div>' +
      '<div class="text-muted" style="font-size:11px">As of today</div>' +
    '</div></div>' +
  '</div>';

  html += '<div class="card border rounded-3 overflow-hidden mb-4"><div class="table-responsive"><table class="table table-hover mb-0" style="font-size:13px">' +
    '<thead style="background:#f8fafc"><tr>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">Installment</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">Amount Due</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">Due Date</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">Status</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11.5px;text-transform:uppercase;letter-spacing:.04em">Action</th>' +
    '</tr></thead><tbody>' +
    '<tr>' +
      '<td class="px-4 py-3 fw-medium">Upon Enrollment (Down Payment)</td>' +
      '<td class="px-4 py-3">' + peso(downPayment) + '</td>' +
      '<td class="px-4 py-3 text-muted">Paid at enrollment</td>' +
      '<td class="px-4 py-3">' + STATUS_BADGES.paid + '</td>' +
      '<td class="px-4 py-3"><span class="text-muted" style="font-size:12px">—</span></td>' +
    '</tr>';

  data.payments.forEach(p => {
    html += '<tr>' +
      '<td class="px-4 py-3 fw-medium">Installment ' + p.installment_number + '</td>' +
      '<td class="px-4 py-3">' + peso(p.amount_due) + '</td>' +
      '<td class="px-4 py-3 text-muted">' + p.due_date + '</td>' +
      '<td class="px-4 py-3">' + (STATUS_BADGES[p.status] || p.status) +
        (p.status === 'unpaid' && p.feedback ? '<div class="mt-1" style="font-size:11px;color:#b45309"><i class="bi bi-exclamation-circle-fill me-1"></i>' + p.feedback + '</div>' : '') +
      '</td>' +
      '<td class="px-4 py-3">' + tuitionActionCell(p) + '</td>' +
    '</tr>';
  });

  html += '</tbody></table></div></div>';

  window._activeTuitionIdx = idx;

  html += '<div class="d-flex align-items-start gap-2 p-3 rounded-3" style="background:#eff6ff;border:1px solid #bfdbfe;font-size:12.5px;color:#1e40af">' +
    '<i class="bi bi-info-circle-fill flex-shrink-0 mt-1"></i>' +
    '<span>Once you upload a proof of payment, it will be sent to the admin for manual verification.</span></div>';

  return html;
}

function tuitionActionCell(p) {
  if (p.status === 'paid') {
    return p.proof_of_payment
      ? '<a href="' + p.proof_of_payment + '" target="_blank" class="btn btn-sm fw-semibold px-3" style="background:#f1f5f9;color:#1a2a5e;font-size:12px;border:1px solid #e2e8f0"><i class="bi bi-eye me-1"></i>View Receipt</a>'
      : '<span class="text-muted" style="font-size:12px">—</span>';
  }
  if (p.status === 'pending') {
    return '<span class="text-muted" style="font-size:12px">Awaiting verification</span>';
  }
  return '<button type="button" class="btn btn-sm fw-semibold px-3" style="background:#1a2a5e;color:#fff;font-size:12px" onclick="openSubmitPaymentModal(' + p.id + ', \'Installment ' + p.installment_number + ' — ' + peso(p.amount_due) + '\')">' +
    '<i class="bi bi-upload me-1"></i>' + (p.feedback ? 'Resubmit Proof' : 'Upload Proof') +
    '</button>';
}

/* ── Submit Payment modal ── */
var _submitPaymentId = null;
var _submitPaymentFile = null;

function openSubmitPaymentModal(paymentId, label) {
  _submitPaymentId = paymentId;
  _submitPaymentFile = null;
  document.getElementById('submitPaymentInstallmentLabel').textContent = label;
  document.querySelectorAll('#submitPaymentMethodCards .pay-method-card').forEach(c => { c.style.background='#fff'; c.style.borderColor='#e2e8f0'; c.style.boxShadow='none'; });
  document.querySelectorAll('input[name="submitPaymentMethod"]').forEach(r => r.checked = false);
  document.getElementById('submitPaymentFile').value = '';
  document.getElementById('submitPaymentFileName').textContent = '';
  document.getElementById('submitPaymentError').classList.add('d-none');
  bootstrap.Modal.getOrCreateInstance(document.getElementById('submitPaymentModal')).show();
}

function selectSubmitPaymentMethod(card, method) {
  document.querySelectorAll('#submitPaymentMethodCards .pay-method-card').forEach(c => { c.style.background='#fff'; c.style.borderColor='#e2e8f0'; c.style.boxShadow='none'; });
  card.style.background='#dbeafe'; card.style.borderColor='#2563eb'; card.style.boxShadow='inset 0 0 0 1px #2563eb';
  card.querySelector('input[name="submitPaymentMethod"]').checked = true;
}

function handleSubmitPaymentFileChange(input) {
  _submitPaymentFile = input.files && input.files[0] ? input.files[0] : null;
  document.getElementById('submitPaymentFileName').textContent = _submitPaymentFile ? '✔ ' + _submitPaymentFile.name : '';
}

function confirmSubmitPayment() {
  var method = document.querySelector('input[name="submitPaymentMethod"]:checked');
  var errorEl = document.getElementById('submitPaymentError');

  if (!method) { errorEl.textContent = 'Please select how you paid.'; errorEl.classList.remove('d-none'); return; }
  if (!_submitPaymentFile) { errorEl.textContent = 'Please upload your proof of payment.'; errorEl.classList.remove('d-none'); return; }
  errorEl.classList.add('d-none');

  var formData = new FormData();
  formData.append('file', _submitPaymentFile);
  formData.append('payment_method', method.value);

  var btn = document.getElementById('submitPaymentBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting…';

  fetch('/tuition/payments/' + _submitPaymentId + '/upload-proof', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Accept': 'application/json' },
    body: formData,
  })
  .then(res => res.json().then(data => ({ ok: res.ok, data })))
  .then(({ ok, data }) => {
    if (ok) {
      bootstrap.Modal.getInstance(document.getElementById('submitPaymentModal')).hide();
      showToast('success', data.message || 'Proof of payment submitted.');
      var visiblePane = document.querySelector('.child-tuition-pane:not(.d-none)');
      if (visiblePane) loadTuitionData(visiblePane.id.replace('tuition-pane-', ''), true);
      loadTuitionHistory();
    } else {
      errorEl.textContent = data.message || 'Upload failed. Please try again.';
      errorEl.classList.remove('d-none');
    }
  })
  .catch(() => { errorEl.textContent = 'An error occurred. Please try again.'; errorEl.classList.remove('d-none'); })
  .finally(() => { btn.disabled = false; btn.innerHTML = '<i class="bi bi-send me-1"></i>Submit'; });
}

/* ── Payment History (combined across all children) ── */
var _tuitionHistoryLoaded = false;
function loadTuitionHistory(force) {
  if (_tuitionHistoryLoaded && !force) return;
  var loadingEl = document.getElementById('tuitionHistoryLoading');
  var contentEl = document.getElementById('tuitionHistoryContent');
  if (!loadingEl || !contentEl) return;

  fetch('{{ route("tuition.history") }}', { headers: { 'Accept': 'application/json' } })
    .then(res => res.json())
    .then(data => {
      _tuitionHistoryLoaded = true;
      loadingEl.classList.add('d-none');
      contentEl.classList.remove('d-none');
      contentEl.innerHTML = renderTuitionHistory(data.history || []);
    })
    .catch(() => { loadingEl.innerHTML = '<span class="text-danger">Could not load payment history.</span>'; });
}

function renderTuitionHistory(rows) {
  if (!rows.length) {
    return '<div class="text-center text-muted py-4" style="font-size:13px">No payments submitted yet.</div>';
  }
  var peso = n => '₱' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

  var html = '<div class="table-responsive"><table class="table table-hover mb-0" style="font-size:13px">' +
    '<thead style="background:#f8fafc"><tr>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11px;text-transform:uppercase">Child</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11px;text-transform:uppercase">Payment</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11px;text-transform:uppercase">Amount</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11px;text-transform:uppercase">Mode</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11px;text-transform:uppercase">Submitted</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11px;text-transform:uppercase">Verified</th>' +
    '<th class="px-4 py-3 fw-semibold" style="color:#64748b;font-size:11px;text-transform:uppercase">Status</th>' +
    '</tr></thead><tbody>';

  rows.forEach(r => {
    html += '<tr>' +
      '<td class="px-4 py-3 fw-medium">' + r.child + '</td>' +
      '<td class="px-4 py-3">' + r.label + '</td>' +
      '<td class="px-4 py-3">' + peso(r.amount) + '</td>' +
      '<td class="px-4 py-3">' + (r.payment_method || '—') + '</td>' +
      '<td class="px-4 py-3 text-muted" style="font-size:12px">' + (r.submitted_at || '—') + '</td>' +
      '<td class="px-4 py-3 text-muted" style="font-size:12px">' + (r.verified_at || '—') + '</td>' +
      '<td class="px-4 py-3">' + (STATUS_BADGES[r.status] || r.status) + '</td>' +
    '</tr>';
  });

  html += '</tbody></table></div>';
  return html;
}

function showLockedToast() {
  showToast('info', 'This section unlocks once your child\'s enrollment has been approved by the administrator.');
}
</script>