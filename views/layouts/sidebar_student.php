<?php
/** Student sidebar. Highlights the active link based on ?page=. */
$current = $_GET['page'] ?? '';
$links = [
    'student_dashboard'     => ['bi-speedometer2', 'Dashboard'],
    'student_profile'       => ['bi-person-circle', 'My Profile'],
    'student_attendance'    => ['bi-calendar-check', 'Attendance'],
    'student_marks'         => ['bi-bar-chart-line', 'Performance / Marks'],
    'student_assignments'   => ['bi-journal-text', 'Assignments'],
    'student_notes'         => ['bi-file-earmark-arrow-down', 'Download Notes'],
    'student_notifications' => ['bi-bell', 'Notifications'],
    'student_change_password' => ['bi-shield-lock', 'Change Password'],
];
?>
<nav class="app-sidebar">
  <a href="<?= BASE_URL ?>index.php?page=student_dashboard" class="brand">
    <i class="bi bi-mortarboard-fill"></i> SMS Student
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
