<?php
$pageTitle = 'Submitted Assignments';
require ROOT_PATH . '/views/layouts/header.php';

$assignmentModel = new Assignment();
$assignmentId = (int) ($_GET['assignment_id'] ?? 0);
$assignment = $assignmentModel->findById($assignmentId);
$submissions = $assignmentId ? $assignmentModel->getSubmissionsForAssignment($assignmentId) : [];
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <a href="<?= BASE_URL ?>index.php?page=admin_assignments" class="btn btn-sm btn-outline-secondary mb-3">
      <i class="bi bi-arrow-left"></i> Back to Assignments
    </a>

    <div class="card p-3">
      <h6 class="mb-3">
        Submissions for: <strong><?= $assignment ? htmlspecialchars($assignment['title']) : 'Unknown Assignment' ?></strong>
      </h6>

      <?php if (empty($submissions)): ?>
        <p class="text-muted mb-0">No submissions yet.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead><tr><th>Roll No</th><th>Student</th><th>Submitted</th><th>File</th><th>Status</th><th>Review</th></tr></thead>
            <tbody>
            <?php foreach ($submissions as $s): ?>
              <tr>
                <td><?= htmlspecialchars($s['roll_no']) ?></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= htmlspecialchars($s['submitted_at']) ?></td>
                <td><a href="<?= BASE_URL ?>assets/uploads/submissions/<?= htmlspecialchars($s['file_path']) ?>" target="_blank"><i class="bi bi-file-earmark-arrow-down"></i> View</a></td>
                <td><span class="badge badge-status-<?= $s['status'] ?>"><?= $s['status'] ?></span>
                  <?php if (!empty($s['remarks'])): ?><div class="small text-muted mt-1"><?= htmlspecialchars($s['remarks']) ?></div><?php endif; ?>
                </td>
                <td>
                  <form action="<?= BASE_URL ?>action.php?controller=Admin&action=reviewSubmission" method="POST" class="d-flex gap-1">
                    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                    <input type="hidden" name="submission_id" value="<?= $s['id'] ?>">
                    <input type="hidden" name="assignment_id" value="<?= $assignmentId ?>">
                    <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Remarks (optional)" style="max-width:150px;">
                    <button type="submit" name="status" value="Approved" class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i></button>
                    <button type="submit" name="status" value="Rejected" class="btn btn-sm btn-danger"><i class="bi bi-x-lg"></i></button>
                  </form>
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

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
