<?php
$pageTitle = 'Admin Dashboard';
require ROOT_PATH . '/views/layouts/header.php';

$studentModel = new Student();
$assignmentModel = new Assignment();
$announcementModel = new Announcement();
$db = Database::getConnection();

$totalStudents = $studentModel->countAll();
$totalAssignments = count($assignmentModel->getAll());
$pendingReview = $db->query(
    "SELECT COUNT(*) c FROM assignment_submissions WHERE status = 'Completed'"
)->fetch()['c'];
$recentAnnouncements = array_slice($announcementModel->getAll(), 0, 5);
?>
<div class="d-flex">
  <?php require ROOT_PATH . '/views/layouts/sidebar_admin.php'; ?>

  <div class="app-content flex-grow-1">
    <?php require ROOT_PATH . '/views/layouts/topbar.php'; ?>
    <?php require ROOT_PATH . '/views/layouts/alerts.php'; ?>

    <div class="row g-3 mb-4">
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-purple"><div class="small">Total Students</div><div class="fs-3 fw-bold"><?= $totalStudents ?></div></div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-teal"><div class="small">Total Assignments</div><div class="fs-3 fw-bold"><?= $totalAssignments ?></div></div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-orange"><div class="small">Submissions to Review</div><div class="fs-3 fw-bold"><?= $pendingReview ?></div></div>
      </div>
      <div class="col-md-3 col-sm-6">
        <div class="stat-card bg-pink"><div class="small">Announcements</div><div class="fs-3 fw-bold"><?= count($announcementModel->getAll()) ?></div></div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-lg-7">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-lightning-charge text-primary"></i> Quick Actions</h6>
          <div class="d-flex flex-wrap gap-2">
            <a href="<?= BASE_URL ?>index.php?page=admin_manage_students" class="btn btn-outline-primary btn-sm"><i class="bi bi-person-plus"></i> Add Student</a>
            <a href="<?= BASE_URL ?>index.php?page=admin_attendance" class="btn btn-outline-primary btn-sm"><i class="bi bi-calendar-check"></i> Mark Attendance</a>
            <a href="<?= BASE_URL ?>index.php?page=admin_assignments" class="btn btn-outline-primary btn-sm"><i class="bi bi-journal-plus"></i> New Assignment</a>
            <a href="<?= BASE_URL ?>index.php?page=admin_notes" class="btn btn-outline-primary btn-sm"><i class="bi bi-upload"></i> Upload Notes</a>
            <a href="<?= BASE_URL ?>index.php?page=admin_announcements" class="btn btn-outline-primary btn-sm"><i class="bi bi-megaphone"></i> Post Announcement</a>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card p-3">
          <h6 class="mb-3"><i class="bi bi-megaphone text-primary"></i> Recent Announcements</h6>
          <?php if (empty($recentAnnouncements)): ?>
            <p class="text-muted mb-0">No announcements yet.</p>
          <?php else: foreach ($recentAnnouncements as $a): ?>
            <div class="mb-2 pb-2 border-bottom">
              <strong class="d-block small"><?= htmlspecialchars($a['title']) ?></strong>
              <span class="text-muted small"><?= htmlspecialchars(mb_strimwidth($a['message'], 0, 80, '...')) ?></span>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require ROOT_PATH . '/views/layouts/footer.php'; ?>
