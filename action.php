<?php
/**
 * Action Dispatcher
 * All forms submit to action.php?controller=X&action=Y
 * This keeps controller logic separate from views (MVC) and
 * gives one single, auditable place where all state-changing
 * requests pass through.
 */
require_once __DIR__ . '/core/bootstrap.php';

$controllerName = $_GET['controller'] ?? '';
$actionName     = $_GET['action'] ?? '';

$allowedControllers = ['Auth', 'Student', 'Admin'];

if (!in_array($controllerName, $allowedControllers, true)) {
    http_response_code(400);
    die('Invalid controller.');
}

$class = $controllerName . 'Controller';
require_once ROOT_PATH . '/controllers/' . $class . '.php';

if (!class_exists($class)) {
    http_response_code(400);
    die('Controller not found.');
}

$controller = new $class();

if (!method_exists($controller, $actionName)) {
    http_response_code(400);
    die('Invalid action.');
}

// Basic CSRF check for state-changing requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!Session::has('csrf_token') || !hash_equals(Session::get('csrf_token'), $token)) {
        http_response_code(403);
        die('Invalid or expired form submission (CSRF check failed). Please go back and try again.');
    }
}

$controller->$actionName();
