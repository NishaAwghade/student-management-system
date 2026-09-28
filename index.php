<?php
/**
 * Front Controller / Router
 * Every page request goes through index.php?page=xxx
 * This keeps a clean, single entry point (MVC style) and
 * prevents direct access to view files.
 */
require_once __DIR__ . '/core/bootstrap.php';

$page = $_GET['page'] ?? 'login_student';

// Map of page keys => view files. Add new pages here.
$publicPages = [
    'login_student' => 'views/auth/login_student.php',
    'login_admin'   => 'views/auth/login_admin.php',
    'register'      => 'views/auth/register.php',
];

$studentPages = [
    'student_dashboard'       => 'views/student/dashboard.php',
    'student_profile'         => 'views/student/profile.php',
    'student_attendance'      => 'views/student/attendance.php',
    'student_marks'           => 'views/student/marks.php',
    'student_assignments'     => 'views/student/assignments.php',
    'student_notes'           => 'views/student/notes.php',
    'student_notifications'   => 'views/student/notifications.php',
    'student_change_password' => 'views/student/change_password.php',
];

$adminPages = [
    'admin_dashboard'         => 'views/admin/dashboard.php',
    'admin_manage_students'   => 'views/admin/manage_students.php',
    'admin_attendance'        => 'views/admin/attendance.php',
    'admin_marks'             => 'views/admin/marks.php',
    'admin_assignments'       => 'views/admin/assignments.php',
    'admin_view_submissions'  => 'views/admin/view_submissions.php',
    'admin_notes'             => 'views/admin/notes.php',
    'admin_announcements'     => 'views/admin/announcements.php',
    'admin_reports'           => 'views/admin/reports.php',
    'admin_change_password'   => 'views/student/change_password.php', // shared view
];

if (isset($publicPages[$page])) {
    // If already logged in, bounce to the right dashboard instead of showing login again
    if (Session::isLoggedIn() && in_array($page, ['login_student', 'login_admin'], true)) {
        $target = Session::role() === 'admin' ? 'admin_dashboard' : 'student_dashboard';
        header('Location: ' . BASE_URL . 'index.php?page=' . $target);
        exit;
    }
    require ROOT_PATH . '/' . $publicPages[$page];
} elseif (isset($studentPages[$page])) {
    Session::requireRole('student');
    require ROOT_PATH . '/' . $studentPages[$page];
} elseif (isset($adminPages[$page])) {
    Session::requireRole('admin');
    require ROOT_PATH . '/' . $adminPages[$page];
} else {
    http_response_code(404);
    echo '404 - Page not found. <a href="' . BASE_URL . '">Go home</a>';
}
