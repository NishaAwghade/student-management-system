<?php
$pageTitle = 'Download Notes';
require ROOT_PATH . '/views/layouts/header.php';

$studentModel = new Student();
$notesModel = new Notes();
$studentId = (int) Session::get('student_id');
$student = $studentModel->findById($studentId);
$notes = $notesModel->getForClass($student['class']);
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_student.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3">
      <?php if (empty($notes)): ?>
        <p class="text-muted">No notes uploaded yet.</p>
      <?php else: foreach ($notes as $n): ?>
        <div class="col-md-4">
          <div class="card p-3 h-100">
            <i class="bi bi-file-earmark-pdf text-danger fs-2"></i>
            <h6 class="mt-2 mb-1"><?= htmlspecialchars($n['title']) ?></h6>
            <p class="small text-muted mb-2"><?= htmlspecialchars($n['subject']) ?></p>
            <a href="<?= BASE_URL ?>assets/uploads/notes/<?= htmlspecialchars($n['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-auto">
              <i class="bi bi-download"></i> Download
            </a>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
