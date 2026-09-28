<?php $pageTitle = 'Admin Login'; require ROOT_PATH . '/views/layouts/header.php'; ?>

<div class="auth-wrapper">
  <div class="auth-card">
    <div class="text-center mb-4">
      <div class="brand"><i class="bi bi-mortarboard-fill"></i> Student Management System</div>
      <p class="text-muted mb-0">Admin / Teacher secure login</p>
    </div>

    <div class="auth-switch-tabs">
      <a href="<?= BASE_URL ?>index.php?page=login_student">Student Login</a>
      <a href="<?= BASE_URL ?>index.php?page=login_admin" class="active">Admin / Teacher Login</a>
    </div>

    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <form action="<?= BASE_URL ?>action.php?controller=Auth&action=loginAdmin" method="POST" class="needs-validation" novalidate>
      <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

      <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" class="form-control" name="email" placeholder="admin@school.com" required>
        <div class="invalid-feedback">Please enter a valid email.</div>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" placeholder="••••••••" required minlength="6">
        <div class="invalid-feedback">Password is required.</div>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-shield-lock"></i> Secure Login
      </button>
    </form>

    <hr>
    <p class="text-center small text-muted mb-0">
      Demo: admin@school.com / Password@123
    </p>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
