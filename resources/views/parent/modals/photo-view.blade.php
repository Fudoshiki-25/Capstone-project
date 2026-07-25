{{-- Modal: full-size profile photo viewer --}}
        <div class="modal fade" id="photoViewModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;background:#0f172a">
              <div class="d-flex justify-content-between align-items-center px-4 py-3">
                <span class="fw-semibold text-white" style="font-size:14px">Profile Photo</span>
                <button class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal"></button>
              </div>
              <div class="d-flex align-items-center justify-content-center p-4" style="min-height:300px">
                <div id="fullPhotoView" class="stu-profile-avatar-lg" style="width:180px;height:180px;font-size:52px">{{ $initials }}</div>
              </div>
              <div class="px-4 pb-4 text-center">
                <div class="fw-semibold text-white" style="font-size:15px">{{ $fullName }}</div>
                <div style="font-size:12px;color:rgba(255,255,255,.5)">{{ $user->email }}</div>
              </div>
            </div>
          </div>
        </div>
