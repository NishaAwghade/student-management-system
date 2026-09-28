<?php
$pageTitle = 'Marks & Performance';
require ROOT_PATH . '/views/layouts/header.php';

$studentModel = new Student();
$marksModel = new Marks();

$students = $studentModel->getAll();
$selectedStudentId = (int) ($_GET['student_id'] ?? ($students[0]['id'] ?? 0));
$selectedStudent = $studentModel->findById($selectedStudentId);
$marks = $selectedStudentId ? $marksModel->getByStudent($selectedStudentId) : [];
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <form class="row g-2 mb-3" method="GET" action="<?= BASE_URL ?>index.php">
      <input type="hidden" name="page" value="admin_marks">
      <div class="col-md-5">
        <select class="form-select" name="student_id" onchange="this.form.submit()">
          <?php foreach ($students as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $s['id'] == $selectedStudentId ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['roll_no']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMarksModal"><i class="bi bi-plus-lg"></i> Add Marks</button>
      </div>
    </form>

    <div class="card p-3">
      <h6 class="mb-3">Marks for <?= $selectedStudent ? htmlspecialchars($selectedStudent['name']) : '-' ?></h6>
      <?php if (empty($marks)): ?>
        <p class="text-muted mb-0">No marks added yet.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead><tr><th>Subject</th><th>Exam</th><th>Marks</th><th>Date</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($marks as $m): ?>
              <tr>
                <td><?= htmlspecialchars($m['subject']) ?></td>
                <td><?= htmlspecialchars($m['exam_type']) ?></td>
                <td><?= $m['marks_obtained'] ?>/<?= $m['total_marks'] ?></td>
                <td><?= htmlspecialchars($m['exam_date'] ?? '-') ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editMarksModal<?= $m['id'] ?>"><i class="bi bi-pencil"></i></button>
                  <a href="<?= BASE_URL ?>action.php?controller=Admin&action=deleteMarks&id=<?= $m['id'] ?>&student_id=<?= $selectedStudentId ?>" class="btn btn-sm btn-outline-danger confirm-delete"><i class="bi bi-trash"></i></a>
                </td>
              </tr>

              <div class="modal fade" id="editMarksModal<?= $m['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <form class="modal-content" action="<?= BASE_URL ?>action.php?controller=Admin&action=editMarks" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                    <input type="hidden" name="mark_id" value="<?= $m['id'] ?>">
                    <input type="hidden" name="student_id" value="<?= $selectedStudentId ?>">
                    <div class="modal-header"><h6 class="modal-title">Edit Marks</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                      <div class="mb-2"><label class="form-label small">Subject</label><input class="form-control" name="subject" value="<?= htmlspecialchars($m['subject']) ?>" required></div>
                      <div class="mb-2"><label class="form-label small">Exam Type</label><input class="form-control" name="exam_type" value="<?= htmlspecialchars($m['exam_type']) ?>" required></div>
                      <div class="row">
                        <div class="col-6 mb-2"><label class="form-label small">Marks Obtained</label><input type="number" step="0.01" class="form-control" name="marks_obtained" value="<?= $m['marks_obtained'] ?>" required></div>
                        <div class="col-6 mb-2"><label class="form-label small">Total Marks</label><input type="number" step="0.01" class="form-control" name="total_marks" value="<?= $m['total_marks'] ?>" required></div>
                      </div>
                      <div class="mb-2"><label class="form-label small">Exam Date</label><input type="date" class="form-control" name="exam_date" value="<?= htmlspecialchars($m['exam_date'] ?? '') ?>"></div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary btn-sm">Save</button></div>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="modal fade" id="addMarksModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" action="<?= BASE_URL ?>action.php?controller=Admin&action=addMarks" method="POST">
      <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
      <input type="hidden" name="student_id" value="<?= $selectedStudentId ?>">
      <div class="modal-header"><h6 class="modal-title">Add Marks for <?= $selectedStudent ? htmlspecialchars($selectedStudent['name']) : '' ?></h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-2"><label class="form-label small">Subject</label><input class="form-control" name="subject" required></div>
        <div class="mb-2"><label class="form-label small">Exam Type</label><input class="form-control" name="exam_type" placeholder="e.g. Midterm" required></div>
        <div class="row">
          <div class="col-6 mb-2"><label class="form-label small">Marks Obtained</label><input type="number" step="0.01" class="form-control" name="marks_obtained" required></div>
          <div class="col-6 mb-2"><label class="form-label small">Total Marks</label><input type="number" step="0.01" class="form-control" name="total_marks" value="100" required></div>
        </div>
        <div class="mb-2"><label class="form-label small">Exam Date</label><input type="date" class="form-control" name="exam_date"></div>
      </div>
      <div class="modal-footer"><button class="btn btn-primary btn-sm">Add</button></div>
    </form>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
