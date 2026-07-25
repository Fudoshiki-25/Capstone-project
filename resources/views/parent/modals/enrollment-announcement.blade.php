{{-- Modal: super-admin announcement, shown on login unless the parent dismissed it for good --}}
@if($popupAnnouncement)
<div class="modal fade" id="announcementPopupModal" tabindex="-1" data-bs-announcement-id="{{ $popupAnnouncement->id }}">
  <div class="modal-dialog modal-dialog-centered" style="max-width:500px">
    <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden">
      <div style="background:linear-gradient(135deg,#1a2a5e 0%,#111d42 100%);padding:28px 28px 22px;position:relative">
        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="d-flex align-items-center gap-3">
          <div style="width:50px;height:50px;border-radius:14px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:24px;color:#fff;flex-shrink:0">
            <i class="bi bi-megaphone-fill"></i>
          </div>
          <div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.6);margin-bottom:2px">Announcement</div>
            <div style="font-size:18px;font-weight:800;color:#fff;line-height:1.2">{{ $popupAnnouncement->title }}</div>
          </div>
        </div>
        <div style="position:absolute;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,.06);top:-20px;right:-20px"></div>
        <div style="position:absolute;width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,.06);bottom:-10px;right:60px"></div>
      </div>
      <div class="modal-body p-4">
        <div style="font-size:14px;color:#374151;line-height:1.7;white-space:pre-line">{{ $popupAnnouncement->message }}</div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4 pt-0 flex-column align-items-stretch gap-3">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="announcementDontShowAgain">
          <label class="form-check-label" for="announcementDontShowAgain" style="font-size:12.5px;color:#64748b">
            Don't show this announcement again
          </label>
        </div>
        <button class="btn btn-sm fw-semibold" style="background:#1a2a5e;color:#fff" data-bs-dismiss="modal">
          Close
        </button>
      </div>
    </div>
  </div>
</div>
@endif
