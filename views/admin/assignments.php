<?php
$pageTitle = 'Assignments';
require ROOT_PATH . '/views/layouts/header.php';

$assignmentModel = new Assignment();
$assignments = $assignmentModel->getAll();
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="mb-0"><i class="bi bi-journal-text text-primary"></i> All Assignments</h6>
      <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
        <i class="bi bi-plus-lg"></i> New Assignment
      </button>
    </div>

    <div class="card p-3">
      <?php if (empty($assignments)): ?>
        <p class="text-muted mb-0">No assignments created yet.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead><tr><th>Title</th><th>Subject</th><th>Class</th><th>Due Date</th><th>Attachment</th><th>Submissions</th></tr></thead>
            <tbody>
            <?php foreach ($assignments as $a): ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($a['title']) ?></strong>
                  <?php if ($a['description']): ?><div class="small text-muted"><?= htmlspecialchars($a['description']) ?></div><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($a['subject']) ?></td>
                <td><?= htmlspecialchars($a['class'] ?? 'All') ?></td>
                <td><?= htmlspecialchars($a['due_date']) ?></td>
                <td>
                  <?php if ($a['file_path']): ?>
                    <a href="<?= BASE_URL ?>assets/uploads/assignments/<?= htmlspecialchars($a['file_path']) ?>" target="_blank"><i class="bi bi-paperclip"></i></a>
                  <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                  <a href="<?= BASE_URL ?>index.php?page=admin_view_submissions&assignment_id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i> View Submissions
                  </a>
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

<div class="modal fade" id="createAssignmentModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" action="<?= BASE_URL ?>action.php?controller=Admin&action=createAssignment" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
      <div class="modal-header"><h6 class="modal-title">New Assignment</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label small">Title</label><input class="form-control" name="title" required></div>
        <div class="mb-2"><label class="form-label small">Description</label><textarea class="form-control" name="description" rows="2"></textarea></div>
        <div class="row">
          <div class="col-6 mb-2"><label class="form-label small">Subject</label><input class="form-control" name="subject" required></div>
          <div class="col-6 mb-2"><label class="form-label small">Class (blank = all)</label><input class="form-control" name="class"></div>
        </div>
        <div class="mb-2"><label class="form-label small">Due Date</label><input type="date" class="form-control" name="due_date" required></div>
        <div class="mb-2"><label class="form-label small">Attachment (optional)</label><input type="file" class="form-control" name="file"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary btn-sm">Create</button></div>
    </form>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
