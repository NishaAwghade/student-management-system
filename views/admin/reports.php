<?php
$pageTitle = 'Reports';
require ROOT_PATH . '/views/layouts/header.php';

$db = Database::getConnection();

// Attendance % per student
$attendanceReport = $db->query(
    "SELECT u.name, s.roll_no, s.class,
            SUM(a.status='Present') AS present_days,
            COUNT(a.id) AS total_days,
            ROUND(SUM(a.status='Present') / NULLIF(COUNT(a.id),0) * 100, 1) AS pct
     FROM students s
     JOIN users u ON u.id = s.user_id
     LEFT JOIN attendance a ON a.student_id = s.id
     GROUP BY s.id
     ORDER BY pct DESC"
)->fetchAll();

// Average marks per class
$performanceReport = $db->query(
    "SELECT s.class, ROUND(AVG(m.marks_obtained / m.total_marks * 100), 1) AS avg_pct, COUNT(m.id) AS total_exams
     FROM marks m
     JOIN students s ON s.id = m.student_id
     GROUP BY s.class"
)->fetchAll();

// Assignment completion stats
$assignmentReport = $db->query(
    "SELECT a.title, a.due_date,
            (SELECT COUNT(*) FROM students) AS total_students,
            SUM(sub.status IN ('Completed','Approved')) AS submitted,
            SUM(sub.status = 'Approved') AS approved
     FROM assignments a
     LEFT JOIN assignment_submissions sub ON sub.assignment_id = a.id
     GROUP BY a.id
     ORDER BY a.due_date DESC"
)->fetchAll();
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3">
      <div class="col-lg-6">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-calendar-check text-primary"></i> Attendance Report</h6>
          <div class="table-responsive" style="max-height:350px; overflow-y:auto;">
            <table class="table table-sm table-hover align-middle">
              <thead><tr><th>Student</th><th>Class</th><th>%</th></tr></thead>
              <tbody>
              <?php foreach ($attendanceReport as $r): ?>
                <tr>
                  <td><?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['roll_no']) ?>)</td>
                  <td><?= htmlspecialchars($r['class']) ?></td>
                  <td><?= $r['pct'] ?? 0 ?>%</td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-bar-chart-line text-primary"></i> Class-wise Average Performance</h6>
          <?php if (empty($performanceReport)): ?>
            <p class="text-muted mb-0">No marks data yet.</p>
          <?php else: foreach ($performanceReport as $p): ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between small mb-1">
                <span>Class <?= htmlspecialchars($p['class']) ?> (<?= $p['total_exams'] ?> exams)</span>
                <span><?= $p['avg_pct'] ?>%</span>
              </div>
              <div class="progress" style="height:8px;">
                <div class="progress-bar bg-teal" style="width:<?= $p['avg_pct'] ?>%; background-color:#06b6d4;"></div>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

      <div class="col-lg-12">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-journal-check text-primary"></i> Assignment Completion</h6>
          <?php if (empty($assignmentReport)): ?>
            <p class="text-muted mb-0">No assignments yet.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead><tr><th>Assignment</th><th>Due Date</th><th>Submitted</th><th>Approved</th><th>Completion</th></tr></thead>
                <tbody>
                <?php foreach ($assignmentReport as $a):
                  $pct = $a['total_students'] > 0 ? round($a['submitted'] / $a['total_students'] * 100, 1) : 0; ?>
                  <tr>
                    <td><?= htmlspecialchars($a['title']) ?></td>
                    <td><?= htmlspecialchars($a['due_date']) ?></td>
                    <td><?= (int)$a['submitted'] ?> / <?= (int)$a['total_students'] ?></td>
                    <td><?= (int)$a['approved'] ?></td>
                    <td>
                      <div class="progress" style="height:8px; min-width:100px;">
                        <div class="progress-bar bg-success" style="width:<?= $pct ?>%"></div>
                      </div>
                      <span class="small text-muted"><?= $pct ?>%</span>
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
