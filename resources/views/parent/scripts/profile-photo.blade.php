<script>
// ─────────────────────────────────────────────
// BLADE VARIABLES — must be declared first
// ─────────────────────────────────────────────
const uploadPicUrl      = "{{ route('parent.profile.uploadPic') }}";
const removePicUrl      = "{{ route('parent.profile.removePic') }}";
const updatePasswordUrl = "{{ route('parent.profile.updatePassword') }}";

const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ─────────────────────────────────────────────
// PROFILE PICTURE — dropzone modal, matches the admin/super-admin flow
// ─────────────────────────────────────────────

function openPhotoModal() { resetPhotoModal(); bootstrap.Modal.getOrCreateInstance(document.getElementById('photoActionModal')).show(); }

function resetPhotoModal() {
    const fileInput = document.getElementById('pp-file');
    fileInput.value = '';
    document.getElementById('ppFileMeta').classList.add('d-none');
    document.getElementById('ppErrorMsg').classList.add('d-none');
    document.getElementById('ppSubmitBtn').disabled = true;
    document.getElementById('ppDropzoneTitle').textContent = 'Drag & drop a photo here';
    // restore preview to current saved photo (or fallback)
    const img = document.getElementById('ppPreviewImg');
    @if($user->profile_pic)
        img.src = '{{ asset("storage/" . $user->profile_pic) }}';
        img.style.display = '';
    @else
        img.style.display = 'none';
        const fb = document.getElementById('ppPreviewFallback');
        if (fb) fb.style.display = 'flex';
    @endif
}

function showPhotoError(msg) {
    const el = document.getElementById('ppErrorMsg');
    el.classList.remove('d-none');
    el.querySelector('span').textContent = msg;
}

function handlePhotoSelect(file) {
    document.getElementById('ppErrorMsg').classList.add('d-none');
    if (!file) return;
    if (!['image/jpeg', 'image/png'].includes(file.type)) { showPhotoError('Please choose a JPG or PNG image.'); return; }
    if (file.size > 2 * 1024 * 1024) { showPhotoError('Image must be 2MB or smaller.'); return; }

    const reader = new FileReader();
    reader.onload = (e) => {
        const img = document.getElementById('ppPreviewImg');
        img.src = e.target.result;
        img.style.display = '';
        const fb = document.getElementById('ppPreviewFallback');
        if (fb) fb.style.display = 'none';
    };
    reader.readAsDataURL(file);

    document.getElementById('ppFileName').textContent = file.name;
    document.getElementById('ppFileMeta').classList.remove('d-none');
    document.getElementById('ppDropzoneTitle').textContent = 'Looking good! Click to choose a different photo';
    document.getElementById('ppSubmitBtn').disabled = false;
}

function clearPhotoSelect() {
    document.getElementById('pp-file').value = '';
    resetPhotoModal();
}

(function setupPhotoDropzone() {
    document.addEventListener('DOMContentLoaded', () => {
        const zone = document.getElementById('ppDropzone');
        if (!zone) return;
        ['dragenter', 'dragover'].forEach(evt => zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.add('pp-dragover'); }));
        ['dragleave', 'drop'].forEach(evt => zone.addEventListener(evt, (e) => { e.preventDefault(); zone.classList.remove('pp-dragover'); }));
        zone.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            if (!file) return;
            document.getElementById('pp-file').files = e.dataTransfer.files;
            handlePhotoSelect(file);
        });
    });
})();

function submitProfilePhoto(e) {
    e.preventDefault();
    const fileInput = document.getElementById('pp-file');
    if (!fileInput.files.length) return;

    const formData = new FormData();
    formData.append('profile_pic', fileInput.files[0]);

    const btn = document.getElementById('ppSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving…';

    fetch(uploadPicUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        body: formData,
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('photoActionModal')).hide();
            showToast('success', data.message);
            setTimeout(() => location.reload(), 600);
        } else {
            showPhotoError(data.message ?? 'Upload failed.');
        }
    })
    .catch(() => showPhotoError('Could not upload the photo. Please try again.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Save Photo';
    });
}

function removePhoto() {
    if (!confirm('Remove your profile photo? You will revert to your initials avatar.')) return;

    fetch(removePicUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({}),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('photoActionModal')).hide();
            showToast('success', data.message);
            setTimeout(() => location.reload(), 600);
        } else {
            showToast('danger', data.message ?? 'Failed to remove photo.');
        }
    })
    .catch(() => showToast('danger', 'An error occurred.'));
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
        showToast('warning', 'Please fill in all password fields.');
        return;
    }
    if (np !== cp) {
        showToast('warning', 'New passwords do not match.');
        return;
    }
    if (np.length < 8 || !/[A-Z]/.test(np) || !/[0-9]/.test(np) || !/[^A-Za-z0-9]/.test(np)) {
        showToast('warning', 'New password must be at least 8 characters and include an uppercase letter, a number, and a special character.');
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
            showToast('success', data.message);
            ['currentPass', 'newPass', 'confirmPass'].forEach(id => {
                document.getElementById(id).value = '';
            });
            document.getElementById('strengthBar').style.width = '0%';
            document.getElementById('strengthLabel').textContent = '';
            document.getElementById('matchMsg').textContent = '';
            setTimeout(() => location.reload(), 600);
        } else {
            showToast('danger', data.message ?? 'Update failed.');
        }
    })
    .catch(() => showToast('danger', 'An error occurred. Please try again.'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-shield-check me-1"></i>Update Password';
    });
}

// showToast(type, message) is the shared helper defined once in toast.blade.php —
// intentionally not redefined here to avoid this script's load order silently
// overriding it for every other script sharing the page.
</script>
