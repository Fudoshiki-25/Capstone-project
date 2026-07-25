{{-- Modal: change/view profile photo action sheet --}}
<div class="modal fade" id="photoActionModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:360px">
    <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden">

      {{-- Header --}}
      <div style="background:#1a2a5e;padding:20px 24px 16px">
        <div class="d-flex align-items-center gap-3">
          <div id="modalAvatarThumb" class="stu-profile-avatar" style="width:46px;height:46px;font-size:15px;flex-shrink:0">
            {{ $initials }}
          </div>
          <div>
            <div class="fw-bold text-white" style="font-size:14px;line-height:1.2">{{ $fullName }}</div>
            <div style="font-size:11px;color:rgba(255,255,255,.6)">Profile Photo</div>
          </div>
        </div>
      </div>

      <div class="p-4">

        {{-- STATE 1: Default action list --}}
        <div id="photoActionList">
          <div class="d-flex flex-column gap-2">

            <button class="btn fw-semibold d-flex align-items-center gap-3 px-3 py-3 rounded-3 text-start"
                    style="background:#f1f5f9;font-size:13.5px;border:none"
                    onclick="viewFullPhoto()">
              <span style="width:36px;height:36px;border-radius:10px;background:#dbeafe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-eye-fill" style="color:#1d4ed8;font-size:15px"></i>
              </span>
              <div>
                <div style="color:#1e293b">View Profile Photo</div>
                <div class="fw-normal text-muted" style="font-size:11px">See your current photo in full size</div>
              </div>
            </button>

            <button class="btn fw-semibold d-flex align-items-center gap-3 px-3 py-3 rounded-3 text-start"
                    style="background:#f1f5f9;font-size:13.5px;border:none"
                    onclick="document.getElementById('profilePicInput').click()">
              <span style="width:36px;height:36px;border-radius:10px;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-cloud-arrow-up-fill" style="color:#16a34a;font-size:15px"></i>
              </span>
              <div>
                <div style="color:#1e293b">Change Photo</div>
                <div class="fw-normal text-muted" style="font-size:11px">Upload a new JPG, PNG, or WebP (max 2 MB)</div>
              </div>
            </button>

            <button class="btn fw-semibold d-flex align-items-center gap-3 px-3 py-3 rounded-3 text-start"
                    style="background:#fff5f5;font-size:13.5px;border:none"
                    onclick="removePhoto()">
              <span style="width:36px;height:36px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <i class="bi bi-trash-fill" style="color:#dc2626;font-size:15px"></i>
              </span>
              <div>
                <div style="color:#dc2626">Remove Photo</div>
                <div class="fw-normal text-muted" style="font-size:11px">Revert to default initials avatar</div>
              </div>
            </button>

          </div>
          <button class="btn btn-sm w-100 mt-3 fw-semibold"
                  style="background:#f1f5f9;color:#64748b;font-size:13px;border:none"
                  data-bs-dismiss="modal">Cancel</button>
        </div>

        {{-- STATE 2: Preview + Save (shown after photo is selected) --}}
        <div id="photoPreviewState" class="d-none">

          <div class="d-flex flex-column align-items-center gap-3 mb-4">
            <div style="position:relative;width:110px;height:110px">
              <img id="modalPreviewImg"
                   src=""
                   alt="Preview"
                   style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid #1a2a5e;">
              <button onclick="document.getElementById('profilePicInput').click()"
                      title="Choose a different photo"
                      style="position:absolute;bottom:0;right:0;width:30px;height:30px;border-radius:50%;background:#1a2a5e;border:2px solid #fff;display:flex;align-items:center;justify-content:center;cursor:pointer">
                <i class="bi bi-pencil-fill text-white" style="font-size:11px"></i>
              </button>
            </div>
            <div class="text-center">
              <div class="fw-semibold" style="font-size:13px;color:#1e293b">Looks good?</div>
              <div class="text-muted" style="font-size:11px">Save to apply this as your profile photo.</div>
            </div>
          </div>

          <button id="modalSaveBtn"
                  class="btn w-100 fw-semibold mb-2"
                  style="background:#1a2a5e;color:#fff;font-size:13px;border-radius:10px"
                  onclick="saveProfilePicFromModal()">
            <i class="bi bi-check-lg me-1"></i>Save Photo
          </button>
          <button class="btn btn-sm w-100 fw-semibold"
                  style="background:#f1f5f9;color:#64748b;font-size:13px;border:none"
                  onclick="discardPhotoPreview()">
            <i class="bi bi-x me-1"></i>Discard
          </button>

        </div>

      </div>
    </div>
  </div>
</div>