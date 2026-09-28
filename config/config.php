<?php
/**
 * Global application configuration.
 * BASE_URL must match the folder name where you placed the project
 * inside XAMPP's htdocs, e.g. http://10.12.205.34/sms/
 */
define('BASE_URL', 'http://10.12.205.34/sms/');
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

define('UPLOAD_ASSIGNMENTS', ROOT_PATH . '/assets/uploads/assignments/');
define('UPLOAD_SUBMISSIONS', ROOT_PATH . '/assets/uploads/submissions/');
define('UPLOAD_NOTES',       ROOT_PATH . '/assets/uploads/notes/');
define('UPLOAD_PROFILE',     ROOT_PATH . '/assets/uploads/profile/');

// Max upload size in bytes (5 MB)
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Allowed file extensions for assignment / notes uploads
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'zip']);

// Session security settings must be set BEFORE session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
// Uncomment the next line once you serve the app over HTTPS in production
// ini_set('session.cookie_secure', 1);

error_reporting(E_ALL);
ini_set('display_errors', 1); // set to 0 in production
