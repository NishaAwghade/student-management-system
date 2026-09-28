<?php
$pageTitle = 'Notifications';
require ROOT_PATH . '/views/layouts/header.php';

$notificationModel = new Notification();
$userId = (int) Session::get('user_id');
$notifications = $notificationModel->getForUser($userId);
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_student.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-bell text-primary"></i> Notifications</h6>
        <form action="<?= BASE_URL ?>action.php?controller=Student&action=markNotificationsRead" method="POST">
          <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
          <button class="btn btn-sm btn-outline-secondary">Mark all as read</button>
        </form>
      </div>

      <?php if (empty($notifications)): ?>
        <p class="text-muted mb-0">No notifications yet.</p>
      <?php else: ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($notifications as $n): ?>
            <li class="list-group-item d-flex justify-content-between align-items-start <?= $n['is_read'] ? '' : 'bg-light' ?>">
              <div>
                <?php if (!$n['is_read']): ?><span class="badge bg-primary me-2">New</span><?php endif; ?>
                <?= htmlspecialchars($n['message']) ?>
              </div>
              <small class="text-muted"><?= htmlspecialchars($n['created_at']) ?></small>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
