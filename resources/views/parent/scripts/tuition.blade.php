{{-- Tuition & Payments panel logic: loads each child's installment schedule
     (down payment + monthly/quarterly installments), lets the parent submit
     proof of payment for any unpaid/needs-resubmit installment — including
     ahead of its due date — and renders the combined payment history table.
     Shares getCsrfToken()/showToast() with the rest of the parent scripts. --}}
<script>
var tuitionPaymentBeingSubmitted = null; // payment id currently open in the modal
var tuitionPaneIndexBeingSubmitted = null; // which child pane to refresh after submit

var TUITION_STATUS_BADGE = {
  paid:            '<span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill">Paid</span>',
  pending:         '<span class="badge bg-warning-subtle text-warning px-3 py-1 rounded-pill">Pending Verification</span>',
  unpaid:          '<span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">Unpaid</span>',
  needs_resubmit:  '<span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill">Needs Resubmission</span>',
};

function installmentLabel(p) {
  return p.installment_number === 0 ? 'Upon Enrollment (Down Payment)' : 'Installment ' + p.installment_number;
}

function loadTuitionPane(index, enrollmentId) {
  var loadingEl = document.getElementById('tuition-loading-' + index);
  var contentEl = document.getElementById('tuition-content-' + index);

  loadingEl.classList.remove('d-none');
  contentEl.classList.add('d-none');

  fetch('{{ route("tuition.show") }}?enrollment_id=' + encodeURIComponent(enrollmentId), {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(function (r) { return r.json(); })
  .then(function (data) {
    contentEl.innerHTML = renderTuitionPane(data, index);
    loadingEl.classList.add('d-none');
    contentEl.classList.remove('d-none');
  })
  .catch(function (err) {
    console.error('Failed to load tuition plan:', err);
    loadingEl.classList.add('d-none');
    contentEl.classList.remove('d-none');
    contentEl.innerHTML = '<div class="text-danger text-center py-4" style="font-size:13px">Could not load billing information. Please refresh the page.</div>';
  });
}

function renderTuitionPane(data, index) {
  if (!data.plan) {
    return '<div class="text-muted text-center py-4" style="font-size:13px">No tuition plan has been set up for this child yet.</div>';
  }

  var plan = data.plan;
  var planLabel = plan.plan_type === 'quarterly' ? 'Quarterly Plan' : 'Monthly Plan (10 months)';

  var html = '';

  // Summary card
  html += '<div class="card border rounded-3 p-4 mb-3">';
  html += '  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">';
  html += '    <div><div class="text-muted" style="font-size:12px">Payment Plan</div><div class="fw-bold" style="font-size:15px;color:#1e293b">' + planLabel + '</div></div>';
  html += '    <div class="text-end"><div class="text-muted" style="font-size:12px">Remaining Balance</div><div class="fw-bold" style="font-size:18px;color:#1a2a5e">₱' + Number(plan.remaining_balance).toLocaleString(undefined, {minimumFractionDigits:2}) + '</div></div>';
  html += '  </div>';
  html += '  <div class="row g-2 text-center">';
  html += '    <div class="col-4"><div class="text-muted" style="font-size:11px">Total Tuition</div><div class="fw-semibold" style="font-size:13px">₱' + Number(plan.total_amount).toLocaleString(undefined, {minimumFractionDigits:2}) + '</div></div>';
  html += '    <div class="col-4"><div class="text-muted" style="font-size:11px">Down Payment</div><div class="fw-semibold" style="font-size:13px">₱' + Number(plan.down_payment).toLocaleString(undefined, {minimumFractionDigits:2}) + '</div></div>';
  html += '    <div class="col-4"><div class="text-muted" style="font-size:11px">Paid So Far</div><div class="fw-semibold text-success" style="font-size:13px">₱' + Number(plan.total_paid).toLocaleString(undefined, {minimumFractionDigits:2}) + '</div></div>';
  html += '  </div>';
  html += '</div>';

  // Installment schedule
  html += '<div class="card border rounded-3 overflow-hidden mb-3">';
  html += '  <div class="p-3 border-bottom bg-light fw-bold" style="font-size:14px;color:#1e293b">Installment Schedule</div>';
  html += '  <div class="d-flex flex-column">';

  data.payments.forEach(function (p) {
    html += '<div class="d-flex align-items-center justify-content-between p-3 border-bottom flex-wrap gap-2">';
    html += '  <div>';
    html += '    <div class="fw-medium" style="font-size:13.5px;color:#1e293b">' + installmentLabel(p) + '</div>';
    html += '    <div class="text-muted" style="font-size:11.5px">Due ' + p.due_date + ' · ₱' + Number(p.amount_due).toLocaleString(undefined, {minimumFractionDigits:2}) + '</div>';
    if (p.status === 'needs_resubmit' && p.feedback) {
      html += '    <div class="text-danger mt-1" style="font-size:11px"><i class="bi bi-exclamation-circle me-1"></i>' + p.feedback + '</div>';
    }
    html += '  </div>';
    html += '  <div class="d-flex align-items-center gap-2">';
    html += TUITION_STATUS_BADGE[p.status] || '';
    if (p.can_submit_proof) {
      html += '<button type="button" class="btn btn-sm btn-navy fw-semibold" style="font-size:12px" onclick="openSubmitPaymentModal(' + p.id + ', \'' + installmentLabel(p).replace(/'/g, "\\'") + '\', ' + index + ')"><i class="bi bi-upload me-1"></i>Pay Now</button>';
    } else if (p.proof_of_payment) {
      html += '<button type="button" class="btn btn-sm btn-outline-secondary" style="font-size:12px" onclick="viewDocument(\'' + p.proof_of_payment + '\', \'' + installmentLabel(p).replace(/'/g, "\\'") + '\')"><i class="bi bi-eye me-1"></i>View</button>';
    }
    html += '  </div>';
    html += '</div>';
  });

  html += '  </div>';
  html += '</div>';

  return html;
}

function switchTuitionChild(index) {
  document.querySelectorAll('.child-tuition-pane').forEach(function (pane, i) {
    pane.classList.toggle('d-none', String(i) !== String(index));
  });

  var pane = document.getElementById('tuition-pane-' + index);
  var contentEl = document.getElementById('tuition-content-' + index);
  // Only fetch the first time this pane is switched to — avoid refetching
  // every time the parent flips back and forth between children.
  if (pane && contentEl && contentEl.innerHTML.trim() === '') {
    loadTuitionPane(index, pane.dataset.enrollmentId);
  }
}

function openSubmitPaymentModal(paymentId, label, paneIndex) {
  tuitionPaymentBeingSubmitted = paymentId;
  tuitionPaneIndexBeingSubmitted = paneIndex;

  document.getElementById('submitPaymentInstallmentLabel').textContent = 'Submitting payment for: ' + label;

  // Reset modal state
  document.querySelectorAll('#submitPaymentMethodCards .pay-method-card').forEach(function (card) {
    card.style.borderColor = '';
    card.style.background = '#fff';
    card.querySelector('input').checked = false;
  });
  document.getElementById('submitPaymentFile').value = '';
  document.getElementById('submitPaymentFileName').textContent = '';
  document.getElementById('submitPaymentError').classList.add('d-none');
  document.getElementById('submitPaymentError').textContent = '';

  var modalEl = document.getElementById('submitPaymentModal');
  var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
  modal.show();
}

function selectSubmitPaymentMethod(el, value) {
  document.querySelectorAll('#submitPaymentMethodCards .pay-method-card').forEach(function (card) {
    card.style.borderColor = '';
    card.style.background = '#fff';
  });
  el.style.borderColor = '#1a2a5e';
  el.style.background = '#f1f5f9';
  el.querySelector('input').checked = true;
}

function handleSubmitPaymentFileChange(input) {
  var nameEl = document.getElementById('submitPaymentFileName');
  nameEl.textContent = input.files.length ? input.files[0].name : '';
}

function confirmSubmitPayment() {
  var errorEl = document.getElementById('submitPaymentError');
  errorEl.classList.add('d-none');
  errorEl.textContent = '';

  var methodInput = document.querySelector('input[name="submitPaymentMethod"]:checked');
  var fileInput = document.getElementById('submitPaymentFile');

  if (!methodInput) {
    errorEl.textContent = 'Please select a mode of payment.';
    errorEl.classList.remove('d-none');
    return;
  }
  if (!fileInput.files.length) {
    errorEl.textContent = 'Please upload proof of payment.';
    errorEl.classList.remove('d-none');
    return;
  }
  if (!tuitionPaymentBeingSubmitted) {
    errorEl.textContent = 'Something went wrong — please close this and try again.';
    errorEl.classList.remove('d-none');
    return;
  }

  var btn = document.getElementById('submitPaymentBtn');
  var originalHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Submitting…';

  var formData = new FormData();
  formData.append('payment_method', methodInput.value);
  formData.append('file', fileInput.files[0]);

  fetch('{{ url("/tuition/payments") }}/' + tuitionPaymentBeingSubmitted + '/upload-proof', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
    body: formData,
  })
  .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
  .then(function (res) {
    btn.disabled = false;
    btn.innerHTML = originalHtml;

    if (!res.ok) {
      errorEl.textContent = res.data.message || 'Could not submit payment. Please try again.';
      errorEl.classList.remove('d-none');
      return;
    }

    var modalEl = document.getElementById('submitPaymentModal');
    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    showToast('success', res.data.message || 'Proof of payment submitted.');

    // Refresh the pane that triggered this, plus the combined history table
    var paneIndex = tuitionPaneIndexBeingSubmitted;
    var contentEl = document.getElementById('tuition-content-' + paneIndex);
    if (contentEl) contentEl.innerHTML = ''; // force refetch
    var pane = document.getElementById('tuition-pane-' + paneIndex);
    if (pane) loadTuitionPane(paneIndex, pane.dataset.enrollmentId);
    loadTuitionHistory();

    tuitionPaymentBeingSubmitted = null;
    tuitionPaneIndexBeingSubmitted = null;
  })
  .catch(function (err) {
    console.error('Payment submission failed:', err);
    btn.disabled = false;
    btn.innerHTML = originalHtml;
    errorEl.textContent = 'Something went wrong. Please try again.';
    errorEl.classList.remove('d-none');
  });
}

function loadTuitionHistory() {
  var loadingEl = document.getElementById('tuitionHistoryLoading');
  var contentEl = document.getElementById('tuitionHistoryContent');

  loadingEl.classList.remove('d-none');
  contentEl.classList.add('d-none');

  fetch('{{ route("tuition.history") }}', {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(function (r) { return r.json(); })
  .then(function (data) {
    contentEl.innerHTML = renderTuitionHistory(data.history || []);
    loadingEl.classList.add('d-none');
    contentEl.classList.remove('d-none');
  })
  .catch(function (err) {
    console.error('Failed to load payment history:', err);
    loadingEl.classList.add('d-none');
    contentEl.classList.remove('d-none');
    contentEl.innerHTML = '<div class="text-danger text-center py-4" style="font-size:13px">Could not load payment history.</div>';
  });
}

function renderTuitionHistory(rows) {
  if (!rows.length) {
    return '<div class="text-muted text-center py-4" style="font-size:13px">No payments submitted yet.</div>';
  }

  var html = '<div class="table-responsive"><table class="table mb-0" style="font-size:13px">';
  html += '<thead><tr class="text-muted" style="font-size:11px;text-transform:uppercase">';
  html += '<th class="ps-3">Child</th><th>Payment</th><th>Amount</th><th>Mode</th><th>Submitted</th><th>Verified</th><th class="pe-3">Status</th>';
  html += '</tr></thead><tbody>';

  rows.forEach(function (row) {
    html += '<tr>';
    html += '<td class="ps-3">' + row.child + '</td>';
    html += '<td>' + row.label + '</td>';
    html += '<td>₱' + Number(row.amount).toLocaleString(undefined, {minimumFractionDigits:2}) + '</td>';
    html += '<td>' + (row.payment_method || '—') + '</td>';
    html += '<td>' + row.submitted_at + '</td>';
    html += '<td>' + (row.verified_at || '—') + '</td>';
    html += '<td class="pe-3">' + (TUITION_STATUS_BADGE[row.status] || row.status) + '</td>';
    html += '</tr>';
  });

  html += '</tbody></table></div>';
  return html;
}

document.addEventListener('DOMContentLoaded', function () {
  var firstPane = document.querySelector('.child-tuition-pane');
  if (firstPane) {
    loadTuitionPane(0, firstPane.dataset.enrollmentId);
  }
  if (document.getElementById('tuitionHistoryContent')) {
    loadTuitionHistory();
  }
});
</script>