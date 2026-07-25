{{-- Special-needs document panel (medical certificate upload outside the main
     Step 2 list). Shares activeEnrollmentId/buildDocRow/DOC_LABELS/handleDocUpload
     with parent.scripts.enrollment-form, which must be loaded on the same page. --}}
<script>
function loadSpecialNeedsPanel() {
  if (!activeEnrollmentId) {
    console.error('loadSpecialNeedsPanel called with no activeEnrollmentId set.');
    return;
  }
  var rowEl  = document.getElementById('sn-doc-row');
  var nameEl = document.getElementById('sn-student-name-label');
  if (nameEl) nameEl.textContent = activeEnrollmentName || '—';
  rowEl.innerHTML = '<div class="text-center py-3 text-muted" style="font-size:13px"><div class="spinner-border spinner-border-sm me-2"></div>Loading…</div>';

  fetch('{{ route("requirements.index") }}?enrollment_id=' + encodeURIComponent(activeEnrollmentId), {
    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => { rowEl.innerHTML = buildDocRow('medical_certificate', '#c0392b', data.requirements || {}); })
  .catch(() => { rowEl.innerHTML = buildDocRow('medical_certificate', '#c0392b', {}); });
}
</script>
