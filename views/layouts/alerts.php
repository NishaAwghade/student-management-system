<?php
/** Renders one-time success/error flash messages. Include inside app-content. */
$success = Session::getFlash('success');
$error   = Session::getFlash('error');
?>
<?php if ($success): ?>
  <div class="alert alert-success alert-dismissible fade show alert-auto-dismiss" role="alert">
    <i class="bi bi-check-circle me-1"></i> <?= htmlspecialchars($success) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="alert alert-danger alert-dismissible fade show alert-auto-dismiss" role="alert">
    <i class="bi bi-exclamation-triangle me-1"></i> <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
