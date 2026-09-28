<?php
$pageTitle = 'My Performance';
require ROOT_PATH . '/views/layouts/header.php';

$marksModel = new Marks();
$studentId = (int) Session::get('student_id');
$marks = $marksModel->getByStudent($studentId);
$averages = $marksModel->getSubjectAverages($studentId);
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_student.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3">
      <div class="col-lg-8">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-bar-chart-line text-primary"></i> All Marks</h6>
          <?php if (empty($marks)): ?>
            <p class="text-muted mb-0">No marks published yet.</p>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead><tr><th>Subject</th><th>Exam</th><th>Marks</th><th>%</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($marks as $m):
                  $pct = $m['total_marks'] > 0 ? round($m['marks_obtained'] / $m['total_marks'] * 100, 1) : 0; ?>
                  <tr>
                    <td><?= htmlspecialchars($m['subject']) ?></td>
                    <td><?= htmlspecialchars($m['exam_type']) ?></td>
                    <td><?= $m['marks_obtained'] ?>/<?= $m['total_marks'] ?></td>
                    <td><?= $pct ?>%</td>
                    <td><?= htmlspecialchars($m['exam_date'] ?? '-') ?></td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-graph-up text-primary"></i> Subject-wise Average</h6>
          <?php if (empty($averages)): ?>
            <p class="text-muted mb-0">No data yet.</p>
          <?php else: foreach ($averages as $a): ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between small mb-1">
                <span><?= htmlspecialchars($a['subject']) ?></span><span><?= $a['avg_pct'] ?>%</span>
              </div>
              <div class="progress" style="height:8px;">
                <div class="progress-bar bg-primary" style="width: <?= $a['avg_pct'] ?>%"></div>
              </div>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
