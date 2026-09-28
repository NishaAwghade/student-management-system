/**
 * Global front-end behaviours:
 * - mobile sidebar toggle
 * - Bootstrap form validation triggers
 * - auto-dismiss alerts
 */
document.addEventListener('DOMContentLoaded', function () {
  // Sidebar toggle (mobile)
  const toggleBtn = document.querySelector('.sidebar-toggle-btn');
  const sidebar = document.querySelector('.app-sidebar');
  if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('show');
    });
  }

  // Bootstrap client-side validation
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });

  // Auto-dismiss alerts after 5s
  document.querySelectorAll('.alert-auto-dismiss').forEach(function (alertEl) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
      bsAlert.close();
    }, 5000);
  });

  // Confirm-before-delete links/buttons
  document.querySelectorAll('.confirm-delete').forEach(function (el) {
    el.addEventListener('click', function (e) {
      if (!confirm('Are you sure you want to delete this? This action cannot be undone.')) {
        e.preventDefault();
      }
    });
  });

  // Password match live-check on registration form
  const pw = document.getElementById('password');
  const confirmPw = document.getElementById('confirm_password');
  if (pw && confirmPw) {
    const check = function () {
      confirmPw.setCustomValidity(pw.value !== confirmPw.value ? 'Passwords do not match' : '');
    };
    pw.addEventListener('input', check);
    confirmPw.addEventListener('input', check);
  }
});
