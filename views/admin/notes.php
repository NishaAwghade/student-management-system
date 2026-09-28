<?php
$pageTitle = 'Upload Notes';
require ROOT_PATH . '/views/layouts/header.php';

$notesModel = new Notes();
$notes = $notesModel->getAll();
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3">
      <div class="col-lg-4">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-upload text-primary"></i> Upload New Notes</h6>
          <form action="<?= BASE_URL ?>action.php?controller=Admin&action=uploadNotes" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <div class="mb-2"><label class="form-label small">Title</label><input class="form-control" name="title" required></div>
            <div class="mb-2"><label class="form-label small">Subject</label><input class="form-control" name="subject" required></div>
            <div class="mb-2"><label class="form-label small">Class (blank = all)</label><input class="form-control" name="class"></div>
            <div class="mb-3"><label class="form-label small">File</label><input type="file" class="form-control" name="file" required></div>
            <button class="btn btn-primary w-100"><i class="bi bi-upload"></i> Upload</button>
          </form>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-file-earmark-text text-primary"></i> Uploaded Notes</h6>
          <?php if (empty($notes)): ?>
            <p class="text-muted mb-0">No notes uploaded yet.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead><tr><th>Title</th><th>Subject</th><th>Class</th><th>Uploaded</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($notes as $n): ?>
                  <tr>
                    <td><?= htmlspecialchars($n['title']) ?></td>
                    <td><?= htmlspecialchars($n['subject']) ?></td>
                    <td><?= htmlspecialchars($n['class'] ?? 'All') ?></td>
                    <td><?= htmlspecialchars($n['created_at']) ?></td>
                    <td>
                      <a href="<?= BASE_URL ?>assets/uploads/notes/<?= htmlspecialchars($n['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                      <a href="<?= BASE_URL ?>action.php?controller=Admin&action=deleteNotes&id=<?= $n['id'] ?>" class="btn btn-sm btn-outline-danger confirm-delete"><i class="bi bi-trash"></i></a>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
