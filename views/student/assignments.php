<?php
$pageTitle = 'Assignments';
require ROOT_PATH . '/views/layouts/header.php';

$studentModel = new Student();
$assignmentModel = new Assignment();
$studentId = (int) Session::get('student_id');
$student = $studentModel->findById($studentId);
$assignments = $assignmentModel->getForStudent($studentId, $student['class']);
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_student.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="card p-3">
      <h6 class="mb-3"><i class="bi bi-journal-text text-primary"></i> My Assignments</h6>
      <?php if (empty($assignments)): ?>
        <p class="text-muted mb-0">No assignments yet.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead><tr><th>Title</th><th>Subject</th><th>Due Date</th><th>Attachment</th><th>Status</th><th>Submit</th></tr></thead>
            <tbody>
            <?php foreach ($assignments as $a):
              $status = $a['submission_status'] ?? 'Pending'; ?>
              <tr>
                <td>
                  <strong><?= htmlspecialchars($a['title']) ?></strong>
                  <?php if ($a['description']): ?><div class="small text-muted"><?= htmlspecialchars($a['description']) ?></div><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($a['subject']) ?></td>
                <td><?= htmlspecialchars($a['due_date']) ?></td>
                <td>
                  <?php if ($a['file_path']): ?>
                    <a href="<?= BASE_URL ?>assets/uploads/assignments/<?= htmlspecialchars($a['file_path']) ?>" target="_blank"><i class="bi bi-paperclip"></i> File</a>
                  <?php else: ?>—<?php endif; ?>
                </td>
                <td><span class="badge badge-status-<?= $status ?>"><?= $status ?></span>
                  <?php if (!empty($a['remarks'])): ?><div class="small text-muted mt-1"><?= htmlspecialchars($a['remarks']) ?></div><?php endif; ?>
                </td>
                <td>
                  <?php if ($status === 'Pending' || $status === 'Rejected'): ?>
                    <form action="<?= BASE_URL ?>action.php?controller=Student&action=submitAssignment" method="POST" enctype="multipart/form-data" class="d-flex gap-1">
                      <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                      <input type="hidden" name="assignment_id" value="<?= $a['id'] ?>">
                      <input type="file" name="submission_file" class="form-control form-control-sm" required style="max-width:170px;">
                      <button class="btn btn-sm btn-primary"><i class="bi bi-upload"></i></button>
                    </form>
                  <?php elseif ($a['submission_file']): ?>
                    <a href="<?= BASE_URL ?>assets/uploads/submissions/<?= htmlspecialchars($a['submission_file']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                      <i class="bi bi-eye"></i> View
                    </a>
                  <?php endif; ?>
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
