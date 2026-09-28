<?php
$pageTitle = 'Student Dashboard';
require ROOT_PATH . '/views/layouts/header.php';

$studentModel = new Student();
$attendanceModel = new Attendance();
$assignmentModel = new Assignment();
$marksModel = new Marks();
$notificationModel = new Notification();

$studentId = (int) Session::get('student_id');
$student = $studentModel->findById($studentId);
$attSummary = $attendanceModel->getSummary($studentId);
$pendingCount = $assignmentModel->countPendingForStudent($studentId, $student['class']);
$recentMarks = array_slice($marksModel->getByStudent($studentId), 0, 5);
$unread = $notificationModel->countUnread((int) Session::get('user_id'));
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_student.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3 mb-4">
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-purple">
          <div class="small">Attendance</div>
          <div class="fs-3 fw-bold"><?= $attSummary['percentage'] ?>%</div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-teal">
          <div class="small">Pending Assignments</div>
          <div class="fs-3 fw-bold"><?= $pendingCount ?></div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-orange">
          <div class="small">Class / Section</div>
          <div class="fs-3 fw-bold"><?= htmlspecialchars($student['class']) ?>-<?= htmlspecialchars($student['section'] ?? '-') ?></div>
        </div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-pink">
          <div class="small">Unread Notifications</div>
          <div class="fs-3 fw-bold"><?= $unread ?></div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-7">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-bar-chart-line text-primary"></i> Recent Marks</h6>
          <?php if (empty($recentMarks)): ?>
            <p class="text-muted mb-0">No marks published yet.</p>
          <?php else: ?>
            <table class="table table-sm align-middle mb-0">
              <thead><tr><th>Subject</th><th>Exam</th><th>Marks</th></tr></thead>
              <tbody>
              <?php foreach ($recentMarks as $m): ?>
                <tr>
                  <td><?= htmlspecialchars($m['subject']) ?></td>
                  <td><?= htmlspecialchars($m['exam_type']) ?></td>
                  <td><?= $m['marks_obtained'] ?>/<?= $m['total_marks'] ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-info-circle text-primary"></i> Welcome, <?= htmlspecialchars($student['name']) ?>!</h6>
          <p class="text-muted small mb-1">Roll No: <?= htmlspecialchars($student['roll_no']) ?></p>
          <p class="text-muted small mb-1">Email: <?= htmlspecialchars($student['email']) ?></p>
          <p class="text-muted small mb-0">Use the sidebar to check attendance, marks, assignments and notes.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
