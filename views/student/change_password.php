<?php
$pageTitle = 'Change Password';
require ROOT_PATH . '/views/layouts/header.php';
$isAdmin = Session::role() === 'admin';
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/' . ($isAdmin ? 'sidebar_admin.php' : 'sidebar_student.php'); ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card p-4">
          <h6 class="mb-3"><i class="bi bi-shield-lock text-primary"></i> Change Password</h6>
          <form action="<?= BASE_URL ?>action.php?controller=Auth&action=changePassword" method="POST" class="needs-validation" novalidate>
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <div class="mb-3">
              <label class="form-label">Current Password</label>
              <input type="password" class="form-control" name="current_password" required>
            </div>
            <div class="mb-3">
              <label class="form-label">New Password</label>
              <input type="password" id="password" class="form-control" name="new_password" required minlength="6">
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm New Password</label>
              <input type="password" id="confirm_password" class="form-control" name="confirm_password" required minlength="6">
            </div>

            <button type="submit" class="btn btn-primary"><i class="bi bi-check2"></i> Update Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
