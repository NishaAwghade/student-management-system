<?php
/** Admin/Teacher sidebar. Highlights the active link based on ?page=. */
$current = $_GET['page'] ?? '';
$links = [
    'admin_dashboard'       => ['bi-speedometer2', 'Dashboard'],
    'admin_manage_students' => ['bi-people', 'Manage Students'],
    'admin_attendance'      => ['bi-calendar-check', 'Mark Attendance'],
    'admin_marks'           => ['bi-bar-chart-line', 'Marks & Performance'],
    'admin_assignments'     => ['bi-journal-text', 'Assignments'],
    'admin_notes'           => ['bi-file-earmark-arrow-up', 'Upload Notes'],
    'admin_announcements'   => ['bi-megaphone', 'Announcements'],
    'admin_reports'         => ['bi-graph-up', 'Reports'],
    'admin_change_password' => ['bi-shield-lock', 'Change Password'],
];
?>
<nav class="app-sidebar">
  <a href="<?= BASE_URL ?>index.php?page=admin_dashboard" class="brand">
    <i class="bi bi-mortarboard-fill"></i> SMS Admin
  </a>
  <ul class="nav flex-column">
    <?php foreach ($links as $key => [$icon, $label]): ?>
      <li class="nav-item">
        <a class="nav-link <?= $current === $key ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php?page=<?= $key ?>">
          <i class="bi <?= $icon ?> me-2"></i><?= $label ?>
        </a>
      </li>
    <?php endforeach; ?>
    <li class="nav-item mt-2">
      <a class="nav-link text-danger" href="<?= BASE_URL ?>action.php?controller=Auth&action=logout">
        <i class="bi bi-box-arrow-right me-2"></i>Logout
      </a>
    </li>
  </ul>
</nav>
