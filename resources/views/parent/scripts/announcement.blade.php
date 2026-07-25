{{-- Shows the active super-admin announcement as a popup once per login,
     unless the parent has checked "don't show this again" for it. --}}
<script>
(function() {
  var STORAGE_KEY = 'phlci_dismissed_announcements';

  function getDismissed() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || []; }
    catch (e) { return []; }
  }

  function showAnnouncementPopup() {
    var el = document.getElementById('announcementPopupModal');
    if (!el) return;

    var announcementId = parseInt(el.dataset.bsAnnouncementId, 10);
    if (getDismissed().includes(announcementId)) return;

    var modal = new bootstrap.Modal(el);
    modal.show();

    el.addEventListener('hidden.bs.modal', function() {
      var checkbox = document.getElementById('announcementDontShowAgain');
      if (checkbox && checkbox.checked) {
        var dismissed = getDismissed();
        if (!dismissed.includes(announcementId)) {
          dismissed.push(announcementId);
          localStorage.setItem(STORAGE_KEY, JSON.stringify(dismissed));
        }
      }
    }, { once: true });
  }

  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', showAnnouncementPopup); }
  else { showAnnouncementPopup(); }
})();
</script>
