{{-- Modal: update profile photo — matches the admin/super-admin dropzone modal --}}
<div class="modal fade" id="photoActionModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content pp-modal">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" style="color:#1e293b">Update Profile Photo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetPhotoModal()"></button>
      </div>
      <form id="profilePhotoForm" onsubmit="submitProfilePhoto(event)">
        <div class="modal-body pt-2">

          <div class="pp-dropzone" id="ppDropzone" onclick="document.getElementById('pp-file').click()">
            <div class="pp-preview-circle" id="ppPreviewCircle">
              @if($user->profile_pic)
                <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="" id="ppPreviewImg">
              @else
                <div class="pp-preview-fallback" id="ppPreviewFallback">{{ $initials }}</div>
                <img src="" alt="" id="ppPreviewImg" style="display:none">
              @endif
            </div>
            <div class="pp-dropzone-text">
              <div class="fw-semibold" style="font-size:13.5px;color:#1e293b" id="ppDropzoneTitle">Drag & drop a photo here</div>
              <div class="text-muted" style="font-size:12px">or <span class="text-navy fw-semibold">click to browse</span> &nbsp;•&nbsp; JPG/PNG, up to 2MB</div>
            </div>
          </div>

          <div id="ppFileMeta" class="d-none mt-3 d-flex align-items-center justify-content-between rounded-3 px-3 py-2" style="background:#f8fafc;border:1px solid #e2e8f0">
            <div class="d-flex align-items-center gap-2" style="font-size:12.5px;color:#475569">
              <i class="bi bi-file-earmark-image text-navy"></i>
              <span id="ppFileName" class="text-truncate" style="max-width:220px"></span>
            </div>
            <button type="button" class="btn btn-sm btn-link text-danger p-0" style="font-size:12px" onclick="clearPhotoSelect()"><i class="bi bi-x-circle"></i> Remove</button>
          </div>

          <div id="ppErrorMsg" class="text-danger mt-2 d-none" style="font-size:12.5px"><i class="bi bi-exclamation-circle me-1"></i><span></span></div>
        </div>
        <div class="modal-footer border-0">
          @if($user->profile_pic)
          <button type="button" class="btn btn-outline-danger btn-sm" onclick="removePhoto()"><i class="bi bi-trash me-1"></i>Remove Current Photo</button>
          @endif
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal" onclick="resetPhotoModal()">Cancel</button>
          <button type="submit" class="btn btn-navy btn-sm fw-semibold" id="ppSubmitBtn" disabled><i class="bi bi-check-lg me-1"></i>Save Photo</button>
        </div>
      </form>
    </div>
  </div>
</div>
