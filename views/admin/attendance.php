<?php
$pageTitle = 'Mark Attendance';
require ROOT_PATH . '/views/layouts/header.php';

$studentModel = new Student();
$attendanceModel = new Attendance();

$classes = $studentModel->getDistinctClasses();
$selectedClass = Validator::sanitize($_GET['class'] ?? ($classes[0] ?? ''));
$date = Validator::sanitize($_GET['date'] ?? date('Y-m-d'));

$rows = $selectedClass ? $attendanceModel->getByClassAndDate($selectedClass, $date) : [];
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <form class="row g-2 mb-3" method="GET" action="<?= BASE_URL ?>index.php">
      <input type="hidden" name="page" value="admin_attendance">
      <div class="col-md-4">
        <label class="form-label small">Class</label>
        <select class="form-select" name="class" onchange="this.form.submit()">
          <?php foreach ($classes as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= $c === $selectedClass ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small">Date</label>
        <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($date) ?>" onchange="this.form.submit()">
      </div>
    </form>

    <div class="card p-3">
      <?php if (empty($rows)): ?>
        <p class="text-muted mb-0">No students found for this class.</p>
      <?php else: ?>
        <form action="<?= BASE_URL ?>action.php?controller=Admin&action=markAttendance" method="POST">
          <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
          <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">

          <div class="table-responsive">
            <table class="table align-middle">
              <thead><tr><th>Roll No</th><th>Name</th><th>Status</th></tr></thead>
              <tbody>
              <?php foreach ($rows as $r): ?>
                <tr>
                  <td><?= htmlspecialchars($r['roll_no']) ?></td>
                  <td><?= htmlspecialchars($r['name']) ?></td>
                  <td>
                    <?php foreach (['Present','Absent','Leave'] as $st): ?>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status[<?= $r['student_id'] ?>]" value="<?= $st ?>"
                               <?= ($r['status'] ?? 'Present') === $st ? 'checked' : '' ?>>
                        <label class="form-check-label small"><?= $st ?></label>
                      </div>
                    <?php endforeach; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check2"></i> Save Attendance</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
