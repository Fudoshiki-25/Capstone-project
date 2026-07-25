<script>
// ─────────────────────────────────────────────
// BLADE VARIABLES — must be declared first
// ─────────────────────────────────────────────
const uploadPicUrl      = "{{ route('parent.profile.uploadPic') }}";
const removePicUrl      = "{{ route('parent.profile.removePic') }}";
const updatePasswordUrl = "{{ route('parent.profile.updatePassword') }}";
const parentInitials    = "{{ $initials }}";
let currentProfileUrl = "{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : '' }}";

const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ─────────────────────────────────────────────
// MODAL HELPERS
// ─────────────────────────────────────────────

function openPhotoModal() {
    pendingProfileFile = null;      // always reset so it opens on the action list
    showPhotoActionList();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('photoActionModal')).show();
}

function showPhotoActionList() {
    document.getElementById('photoActionList').classList.remove('d-none');
    document.getElementById('photoPreviewState').classList.add('d-none');
}

function showPhotoPreviewState(dataUrl) {
    document.getElementById('modalPreviewImg').src = dataUrl;
    document.getElementById('photoActionList').classList.add('d-none');
    document.getElementById('photoPreviewState').classList.remove('d-none');
}

// ─────────────────────────────────────────────
// PROFILE PICTURE
// ─────────────────────────────────────────────

let pendingProfileFile = null;

function handleProfilePicChange(input) {
    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    pendingProfileFile = file;

    const reader = new FileReader();
    reader.onload = (e) => {
        const dataUrl = e.target.result;

        document.getElementById('profileAvatarPreview').innerHTML =
            `<img src="${dataUrl}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Preview">`;

        document.getElementById('modalAvatarThumb').innerHTML =
            `<img src="${dataUrl}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Preview">`;

        showPhotoPreviewState(dataUrl);
        bootstrap.Modal.getOrCreateInstance(document.getElementById('photoActionModal')).show();
    };
    reader.readAsDataURL(file);

    input.value = '';
}

function saveProfilePicFromModal() {
    if (!pendingProfileFile) {
        showToast('No photo selected.', 'warning');
        return;
    }

    const formData = new FormData();
    formData.append('profile_pic', pendingProfileFile);
    formData.append('_token', CSRF);

    const btn = document.getElementById('modalSaveBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

fetch(uploadPicUrl, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
    },
    body: formData,
})
.then(r => r.json())
.then(data => {
    if (data.success) {
        pendingProfileFile = null;
        currentProfileUrl  = data.url;

        const img = `<img src="${data.url}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Profile">`;
        document.getElementById('profileAvatarPreview').innerHTML = img;
        document.getElementById('modalAvatarThumb').innerHTML     = img;
        document.getElementById('sidebarAvatar').innerHTML        = img; 
        document.getElementById('topbarAvatar').innerHTML         = img;

        bootstrap.Modal.getOrCreateInstance(document.getElementById('photoActionModal')).hide();
        showPhotoActionList();
        showToast(data.message, 'success');
        setTimeout(() => location.reload(), 600);
    } else {
        showToast(data.message ?? 'Upload failed.', 'danger');
    }
})
.catch(err => {
    console.log('Catch error:', err);  // ← add this
    showToast('An error occurred. Please try again.', 'danger');
})
.finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save Photo';
});
}

function discardPhotoPreview() {
    pendingProfileFile = null;

    if (currentProfileUrl) {
        const img = `<img src="${currentProfileUrl}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Profile">`;
        document.getElementById('profileAvatarPreview').innerHTML = img;
        document.getElementById('modalAvatarThumb').innerHTML     = img;
    } else {
        document.getElementById('profileAvatarPreview').innerHTML = parentInitials;
        document.getElementById('modalAvatarThumb').innerHTML     = parentInitials;
    }

    showPhotoActionList();
}

function removePhoto() {
    if (!confirm('Remove your profile photo? You will revert to your initials avatar.')) return;

    bootstrap.Modal.getOrCreateInstance(document.getElementById('photoActionModal')).hide();

    fetch(removePicUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            currentProfileUrl  = null;
            pendingProfileFile = null;

            document.getElementById('profileAvatarPreview').innerHTML = parentInitials;
            document.getElementById('modalAvatarThumb').innerHTML     = parentInitials;
            document.getElementById('fullPhotoView').innerHTML        = parentInitials;
            document.getElementById('sidebarAvatar').innerHTML        = parentInitials;

            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(data.message ?? 'Failed to remove photo.', 'danger');
        }
    })
    .catch(() => showToast('An error occurred.', 'danger'));
}

function viewFullPhoto() {
    bootstrap.Modal.getOrCreateInstance(document.getElementById('photoActionModal')).hide();

    const fullView = document.getElementById('fullPhotoView');
    if (currentProfileUrl) {
        fullView.innerHTML = `<img src="${currentProfileUrl}"
            style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Profile">`;
    }

    setTimeout(() => {
        bootstrap.Modal.getOrCreateInstance(document.getElementById('photoViewModal')).show();
    }, 300);
}

// ─────────────────────────────────────────────
// PASSWORD UPDATE
// ─────────────────────────────────────────────

function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function checkPasswordStrength(val) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    let score   = 0;

    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { pct: '0%',   bg: '',        text: '' },
        { pct: '25%',  bg: '#ef4444', text: 'Weak' },
        { pct: '50%',  bg: '#f97316', text: 'Fair' },
        { pct: '75%',  bg: '#eab308', text: 'Good' },
        { pct: '100%', bg: '#22c55e', text: 'Strong' },
    ];

    const level = levels[score] ?? levels[0];
    bar.style.width      = level.pct;
    bar.style.background = level.bg;
    label.textContent    = level.text;
    label.style.color    = level.bg;
}

function checkMatch() {
    const np  = document.getElementById('newPass').value;
    const cp  = document.getElementById('confirmPass').value;
    const msg = document.getElementById('matchMsg');

    if (!cp) { msg.textContent = ''; return; }

    if (np === cp) {
        msg.textContent = '✓ Passwords match';
        msg.style.color = '#22c55e';
    } else {
        msg.textContent = '✗ Passwords do not match';
        msg.style.color = '#ef4444';
    }
}

function updatePassword() {
    const current = document.getElementById('currentPass').value.trim();
    const np      = document.getElementById('newPass').value;
    const cp      = document.getElementById('confirmPass').value;

    if (!current || !np || !cp) {
        showToast('Please fill in all password fields.', 'warning');
        return;
    }
    if (np !== cp) {
        showToast('New passwords do not match.', 'warning');
        return;
    }
    if (np.length < 8 || !/[A-Z]/.test(np) || !/[0-9]/.test(np) || !/[^A-Za-z0-9]/.test(np)) {
        showToast('New password must be at least 8 characters and include an uppercase letter, a number, and a special character.', 'warning');
        return;
    }

    const btn = document.getElementById('updatePasswordBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Updating…';

    fetch(updatePasswordUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            current_password:          current,
            new_password:              np,
            new_password_confirmation: cp,
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            ['currentPass', 'newPass', 'confirmPass'].forEach(id => {
                document.getElementById(id).value = '';
            });
            document.getElementById('strengthBar').style.width = '0%';
            document.getElementById('strengthLabel').textContent = '';
            document.getElementById('matchMsg').textContent = '';
            setTimeout(() => location.reload(), 600);
        } else {
            showToast(data.message ?? 'Update failed.', 'danger');
        }
    })
    .catch(() => showToast('An error occurred. Please try again.', 'danger'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Update Password';
    });
}

// ─────────────────────────────────────────────
// TOAST HELPER
// ─────────────────────────────────────────────

function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) { alert(message); return; }

    const colors = { success: '#1a2a5e', danger: '#dc2626', warning: '#d97706' };
    const id = 'toast-' + Date.now();

    container.insertAdjacentHTML('beforeend', `
        <div id="${id}" class="toast align-items-center text-white border-0 show mb-2"
             style="background:${colors[type] ?? colors.success};min-width:260px"
             role="alert">
            <div class="d-flex">
                <div class="toast-body" style="font-size:13px">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>`);

    setTimeout(() => document.getElementById(id)?.remove(), 3500);
}
</script>
