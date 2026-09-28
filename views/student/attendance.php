<?php
$pageTitle = 'My Attendance';
require ROOT_PATH . '/views/layouts/header.php';

$attendanceModel = new Attendance();
$studentId = (int) Session::get('student_id');
$records = $attendanceModel->getByStudent($studentId);
$summary = $attendanceModel->getSummary($studentId);
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_student.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3 mb-3">
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-purple"><div class="small">Overall %</div><div class="fs-3 fw-bold"><?= $summary['percentage'] ?>%</div></div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-teal"><div class="small">Present</div><div class="fs-3 fw-bold"><?= (int)$summary['present_count'] ?></div></div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-orange"><div class="small">Absent</div><div class="fs-3 fw-bold"><?= (int)$summary['absent_count'] ?></div></div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-pink"><div class="small">Leave</div><div class="fs-3 fw-bold"><?= (int)$summary['leave_count'] ?></div></div>
      </div>
    </div>

    <div class="card p-3">
      <h6 class="mb-3"><i class="bi bi-calendar-check text-primary"></i> Attendance History</h6>
      <?php if (empty($records)): ?>
        <p class="text-muted mb-0">No attendance records yet.</p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead><tr><th>Date</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($records as $r): ?>
              <tr>
                <td><?= htmlspecialchars($r['date']) ?></td>
                <td><span class="badge badge-status-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
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
