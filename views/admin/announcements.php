<?php
$pageTitle = 'Announcements';
require ROOT_PATH . '/views/layouts/header.php';

$announcementModel = new Announcement();
$announcements = $announcementModel->getAll();
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3">
      <div class="col-lg-4">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-megaphone text-primary"></i> New Announcement</h6>
          <form action="<?= BASE_URL ?>action.php?controller=Admin&action=createAnnouncement" method="POST">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <div class="mb-2"><label class="form-label small">Title</label><input class="form-control" name="title" required></div>
            <div class="mb-3"><label class="form-label small">Message</label><textarea class="form-control" name="message" rows="4" required></textarea></div>
            <button class="btn btn-primary w-100"><i class="bi bi-send"></i> Post to All Students</button>
          </form>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-list-ul text-primary"></i> All Announcements</h6>
          <?php if (empty($announcements)): ?>
            <p class="text-muted mb-0">No announcements posted yet.</p>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($announcements as $a): ?>
                <li class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <strong><?= htmlspecialchars($a['title']) ?></strong>
                    <a href="<?= BASE_URL ?>action.php?controller=Admin&action=deleteAnnouncement&id=<?= $a['id'] ?>" class="text-danger confirm-delete"><i class="bi bi-trash"></i></a>
                  </div>
                  <p class="mb-1 small"><?= nl2br(htmlspecialchars($a['message'])) ?></p>
                  <small class="text-muted">By <?= htmlspecialchars($a['author']) ?> on <?= htmlspecialchars($a['created_at']) ?></small>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
