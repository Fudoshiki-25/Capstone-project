{{-- ===== PANEL: MY PROFILE ===== --}}
    <div id="panel-my-profile" class="panel-section d-none p-3 p-md-4">
      <div class="fw-bold mb-1" style="font-size:20px;color:#1e293b">My Profile</div>
      <div class="text-muted mb-4" style="font-size:14px">Manage your profile picture and account password</div>
      <div style="max-width:780px">

        <!-- Profile Picture -->
        <div class="card border rounded-3 p-4 mb-4">
          <div class="fw-bold mb-3 pb-3" style="font-size:15px;color:#1e293b;border-bottom:1px solid #f1f5f9"><i class="bi bi-image-fill me-2 text-navy"></i>Profile Picture</div>
          <div class="d-flex flex-column align-items-center text-center py-2">
            <div class="pp-avatar-wrap" onclick="openPhotoModal()" role="button" tabindex="0" title="Change profile photo">
              @if($user->profile_pic)
                <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Profile photo" class="pp-avatar-img" id="ppCurrentImg">
              @else
                <div class="pp-avatar-fallback" id="ppCurrentFallback">{{ $initials }}</div>
              @endif
              <div class="pp-avatar-overlay"><i class="bi bi-camera-fill"></i></div>
              <div class="pp-avatar-badge"><i class="bi bi-camera-fill"></i></div>
            </div>
            <div class="fw-semibold mt-3" style="font-size:14.5px;color:#1e293b">{{ $fullName }}</div>
            <div class="text-muted" style="font-size:13px">{{ $user->email }}</div>
            <button type="button" class="btn btn-outline-navy btn-sm fw-semibold mt-3" onclick="openPhotoModal()">
              <i class="bi bi-pencil-fill me-1"></i>Change Photo
            </button>
          </div>
        </div>

        <input type="file" id="pp-file" name="profile_pic" accept="image/png, image/jpeg" class="d-none" onchange="handlePhotoSelect(this.files[0])">

        @include('parent.modals.photo-action')

        <!-- Change Password -->
        <div class="card border rounded-3 p-4">
          <div class="fw-bold mb-3 pb-3" style="font-size:15px;color:#1e293b;border-bottom:1px solid #f1f5f9"><i class="bi bi-shield-lock-fill me-2 text-navy"></i>Change Password</div>

          <div class="alert d-flex align-items-start gap-2 mb-4" style="background:var(--navy-light);border:1px solid #c7d2ea;border-radius:10px">
            <i class="bi bi-info-circle-fill" style="color:var(--navy);margin-top:2px"></i>
            <div style="font-size:13px;color:var(--navy)">For your security, choose a strong password with at least 8 characters including uppercase, lowercase, numbers, and symbols.</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium" style="font-size:13px">Current Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="password" class="form-control" id="currentPass" placeholder="Enter your current password">
              <button class="btn btn-outline-secondary" type="button" onclick="togglePass('currentPass',this)"><i class="bi bi-eye"></i></button>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-medium" style="font-size:13px">New Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control" id="newPass" placeholder="Enter new password" oninput="checkPasswordStrength(this.value); updatePasswordChecklist(this.value, 'newPass-req')">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('newPass',this)"><i class="bi bi-eye"></i></button>
              </div>
              <div class="mt-2">
                <div class="progress" style="height:5px;border-radius:4px">
                  <div id="strengthBar" class="progress-bar" style="width:0%;transition:width .3s,background .3s"></div>
                </div>
                <div id="strengthLabel" class="text-muted mt-1" style="font-size:11px"></div>
              </div>
              @include('partials.password-checklist', ['prefix' => 'newPass-req'])
            </div>
            <div class="col-md-6">
              <label class="form-label fw-medium" style="font-size:13px">Confirm New Password <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="password" class="form-control" id="confirmPass" placeholder="Re-enter new password" oninput="checkMatch()">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('confirmPass',this)"><i class="bi bi-eye"></i></button>
              </div>
              <div id="matchMsg" class="mt-1" style="font-size:11px"></div>
            </div>
          </div>
          <button class="btn btn-navy fw-semibold mt-4" id="updatePasswordBtn" onclick="updatePassword()">
            <i class="bi bi-shield-check me-1"></i>Update Password
          </button>
        </div>
      </div>
    </div>




@push('script')
    @include('parent.scripts.profile-photo')
@endpush