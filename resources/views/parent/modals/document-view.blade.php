{{-- Modal: preview an uploaded requirement/proof-of-payment image without leaving the page --}}
<div class="modal fade" id="documentViewModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden">
      <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
        <span class="fw-semibold" id="documentViewModalLabel" style="font-size:14px;color:#1e293b">Document Preview</span>
        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="d-flex align-items-center justify-content-center p-3" style="background:#f8fafc;min-height:300px">
        <img id="documentViewModalImg" src="" alt="" style="max-width:100%;max-height:70vh;object-fit:contain;border-radius:8px">
      </div>
    </div>
  </div>
</div>
