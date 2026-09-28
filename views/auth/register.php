<?php $pageTitle = 'Student Registration'; require ROOT_PATH . '/views/layouts/header.php'; ?>

<div class="auth-wrapper">
  <div class="auth-card" style="max-width: 520px;">
    <div class="text-center mb-4">
      <div class="brand"><i class="bi bi-mortarboard-fill"></i> Student Registration</div>
      <p class="text-muted mb-0">Only students can self-register. Teachers are added by the admin.</p>
    </div>

    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <form action="<?= BASE_URL ?>action.php?controller=Auth&action=registerStudent" method="POST" class="needs-validation" novalidate>
      <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" name="name" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Email address</label>
          <input type="email" class="form-control" name="email" required>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4 mb-3">
          <label class="form-label">Roll Number</label>
          <input type="text" class="form-control" name="roll_no" required>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Class</label>
          <input type="text" class="form-control" name="class" placeholder="e.g. 10" required>
        </div>
        <div class="col-md-4 mb-3">
          <label class="form-label">Section</label>
          <input type="text" class="form-control" name="section" placeholder="e.g. A">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" class="form-control" name="phone">
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Password</label>
          <input type="password" id="password" class="form-control" name="password" required minlength="6">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" id="confirm_password" class="form-control" name="confirm_password" required minlength="6">
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-person-plus"></i> Register
      </button>

      <p class="text-center mt-3 mb-0 small">
        Already have an account? <a href="<?= BASE_URL ?>index.php?page=login_student">Login here</a>
      </p>
    </form>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
