{{-- ===== PANEL: TUITION & PAYMENTS ===== --}}
    <div id="panel-tuition-payments" class="panel-section d-none p-3 p-md-4">
      <div class="fw-bold mb-1" style="font-size:22px;color:#1e293b">Tuition &amp; Payments</div>
      <div class="text-muted mb-4" style="font-size:14px">Monitor billing statements, balances, and submit proofs of payment</div>

      @if(!$hasEnrolledChildren)
        <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center" style="max-width:480px;margin:0 auto">
          <div style="width:72px;height:72px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin-bottom:20px">
            <i class="bi bi-lock-fill" style="font-size:28px;color:#94a3b8"></i>
          </div>
          <div class="fw-bold mb-2" style="font-size:18px;color:#1e293b">Access Restricted</div>
          <div class="text-muted mb-4" style="font-size:13.5px;line-height:1.7">
            Tuition &amp; Payments becomes available once your child's enrollment has been approved by the school administrator.
          </div>
          <button class="btn fw-semibold px-4" style="background:#1a2a5e;color:#fff;font-size:13px" onclick="showPanel('home'); showHomeSubPanel('enrollment-form')">
            <i class="bi bi-pencil-fill me-2"></i>Go to Enrollment
          </button>
        </div>
      @else
        <div style="max-width:900px">
          @if($approvedChildren->count() > 1)
          <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3 bg-white border" style="max-width:420px">
            <label class="fw-semibold flex-shrink-0" style="font-size:13px;color:#374151">
              <i class="bi bi-person-fill me-1" style="color:#1a2a5e"></i>Select Child:
            </label>
            <select class="form-select form-select-sm border-0" id="tuitionChildSelector" onchange="switchTuitionChild(this.value)" style="font-size:13px;background:transparent">
              @foreach($approvedChildren as $i => $child)
              <option value="{{ $i }}">{{ $child->first_name }} {{ $child->last_name }} ({{ $child->grade_level }})</option>
              @endforeach
            </select>
          </div>
          @endif

          @foreach($approvedChildren as $i => $child)
          <div class="child-tuition-pane {{ $i !== 0 ? 'd-none' : '' }}" id="tuition-pane-{{ $i }}" data-enrollment-id="{{ $child->id }}">
            <div id="tuition-loading-{{ $i }}" class="text-center py-4 text-muted" style="font-size:13px">
              <div class="spinner-border spinner-border-sm me-2"></div> Loading billing information…
            </div>
            <div id="tuition-content-{{ $i }}" class="d-none"></div>
          </div>
          @endforeach

          <!-- Payment History (combined across all children) -->
          <div class="card border rounded-3 overflow-hidden mt-4">
            <div class="p-3 border-bottom bg-light">
              <div class="fw-bold" style="font-size:14px;color:#1e293b"><i class="bi bi-clock-history me-2 text-navy"></i>Payment History</div>
              <div class="text-muted" style="font-size:12px">Every payment you've submitted, across all your children</div>
            </div>
            <div id="tuitionHistoryLoading" class="text-center py-4 text-muted" style="font-size:13px">
              <div class="spinner-border spinner-border-sm me-2"></div> Loading payment history…
            </div>
            <div id="tuitionHistoryContent" class="d-none"></div>
          </div>
        </div>
      @endif
    </div>

    <!-- ===== MODAL: SUBMIT PAYMENT ===== -->
    <div class="modal fade" id="submitPaymentModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered" style="max-width:440px">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px">
          <div class="modal-header border-0 pb-0 px-4 pt-4">
            <h5 class="modal-title fw-bold" style="color:#1e293b;font-size:16px">Submit Payment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body px-4">
            <div class="text-muted mb-3" style="font-size:12.5px" id="submitPaymentInstallmentLabel"></div>

            <div class="mb-3">
              <label class="form-label fw-medium" style="font-size:12.5px">Mode of Payment <span class="text-danger">*</span></label>
              <div class="d-flex flex-wrap gap-2" id="submitPaymentMethodCards">
                <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:100px" onclick="selectSubmitPaymentMethod(this,'gcash')">
                  <input type="radio" name="submitPaymentMethod" value="gcash" style="display:none">
                  <span style="font-size:13px;font-weight:700;color:#1e293b">GCash</span>
                </label>
                <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:100px" onclick="selectSubmitPaymentMethod(this,'maya')">
                  <input type="radio" name="submitPaymentMethod" value="maya" style="display:none">
                  <span style="font-size:13px;font-weight:700;color:#1e293b">Maya</span>
                </label>
                <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:120px" onclick="selectSubmitPaymentMethod(this,'bank_transfer')">
                  <input type="radio" name="submitPaymentMethod" value="bank_transfer" style="display:none">
                  <span style="font-size:13px;font-weight:700;color:#1e293b">Bank Transfer</span>
                </label>
                <label class="pay-method-card d-flex align-items-center gap-2 px-3 py-2 rounded-3 border" style="cursor:pointer;background:#fff;min-width:90px" onclick="selectSubmitPaymentMethod(this,'cash')">
                  <input type="radio" name="submitPaymentMethod" value="cash" style="display:none">
                  <span style="font-size:13px;font-weight:700;color:#1e293b">Cash</span>
                </label>
              </div>
            </div>

            <div class="mb-2">
              <label class="form-label fw-medium" style="font-size:12.5px">Proof of Payment <span class="text-danger">*</span></label>
              <label class="d-flex align-items-center gap-2 p-3 rounded-3 border" style="cursor:pointer;background:#f8fafc;border-style:dashed!important">
                <i class="bi bi-cloud-arrow-up" style="font-size:20px;color:#94a3b8;flex-shrink:0"></i>
                <div>
                  <div style="font-size:12.5px;font-weight:600;color:#374151">Click to upload screenshot or receipt</div>
                  <div style="font-size:11px;color:#94a3b8">JPG, PNG or PDF · max 5MB</div>
                </div>
                <input type="file" id="submitPaymentFile" accept="image/*,.pdf" style="display:none" onchange="handleSubmitPaymentFileChange(this)">
              </label>
              <span id="submitPaymentFileName" class="d-block mt-2 text-muted" style="font-size:12px"></span>
            </div>
            <div id="submitPaymentError" class="text-danger d-none" style="font-size:12px"></div>
          </div>
          <div class="modal-footer border-0 px-4 pb-4">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-navy btn-sm fw-semibold px-4" id="submitPaymentBtn" onclick="confirmSubmitPayment()">
              <i class="bi bi-send me-1"></i>Submit
            </button>
          </div>
        </div>
      </div>
    </div>
