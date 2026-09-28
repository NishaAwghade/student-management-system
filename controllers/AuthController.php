<?php
/**
 * AuthController
 * Handles student registration, student login, admin login,
 * logout and change-password actions.
 * Security: password_hash/password_verify, prepared statements (via models),
 * input validation/sanitisation, session regeneration on login.
 */
class AuthController
{
    private User $userModel;
    private Student $studentModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->studentModel = new Student();
    }

    /** POST: Student self-registration */
    public function registerStudent(): void
    {
        $name     = Validator::sanitize($_POST['name'] ?? '');
        $email    = Validator::sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $rollNo   = Validator::sanitize($_POST['roll_no'] ?? '');
        $class    = Validator::sanitize($_POST['class'] ?? '');
        $section  = Validator::sanitize($_POST['section'] ?? '');
        $phone    = Validator::sanitize($_POST['phone'] ?? '');

        $errors = [];
        if (!Validator::isRequired($name)) $errors[] = 'Name is required.';
        if (!Validator::isEmail($email)) $errors[] = 'A valid email is required.';
        if (!Validator::isStrongPassword($password)) $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm) $errors[] = 'Passwords do not match.';
        if (!Validator::isRequired($rollNo)) $errors[] = 'Roll number is required.';
        if (!Validator::isRequired($class)) $errors[] = 'Class is required.';
        if ($this->userModel->emailExists($email)) $errors[] = 'This email is already registered.';

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            header('Location: ' . BASE_URL . 'index.php?page=register');
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->userModel->createStudentUser($name, $email, $hash);

        $this->studentModel->create([
            'user_id' => $userId,
            'roll_no' => $rollNo,
            'class'   => $class,
            'section' => $section,
            'phone'   => $phone,
        ]);

        Session::setFlash('success', 'Registration successful! You can now log in.');
        header('Location: ' . BASE_URL . 'index.php?page=login_student');
        exit;
    }

    /** POST: Student login */
    public function loginStudent(): void
    {
        $this->login('student', $_POST['email'] ?? '', $_POST['password'] ?? '');
    }

    /** POST: Admin/Teacher login */
    public function loginAdmin(): void
    {
        $this->login('admin', $_POST['email'] ?? '', $_POST['password'] ?? '');
    }

    private function login(string $expectedRole, string $email, string $password): void
    {
        $email = Validator::sanitize($email);
        $loginPage = $expectedRole === 'admin' ? 'login_admin' : 'login_student';

        if (!Validator::isEmail($email) || !Validator::isRequired($password)) {
            Session::setFlash('error', 'Please enter a valid email and password.');
            header('Location: ' . BASE_URL . 'index.php?page=' . $loginPage);
            exit;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Session::setFlash('error', 'Invalid email or password.');
            header('Location: ' . BASE_URL . 'index.php?page=' . $loginPage);
            exit;
        }

        if ($user['role'] !== $expectedRole) {
            Session::setFlash('error', 'Please use the correct login portal for your account type.');
            header('Location: ' . BASE_URL . 'index.php?page=' . $loginPage);
            exit;
        }

        if ($user['status'] !== 'active') {
            Session::setFlash('error', 'Your account has been deactivated. Contact the administrator.');
            header('Location: ' . BASE_URL . 'index.php?page=' . $loginPage);
            exit;
        }

        // Prevent session fixation
        session_regenerate_id(true);

        Session::set('user_id', (int)$user['id']);
        Session::set('name', $user['name']);
        Session::set('role', $user['role']);

        if ($user['role'] === 'student') {
            $student = $this->studentModel->findByUserId((int)$user['id']);
            Session::set('student_id', $student['id'] ?? null);
            header('Location: ' . BASE_URL . 'index.php?page=student_dashboard');
        } else {
            header('Location: ' . BASE_URL . 'index.php?page=admin_dashboard');
        }
        exit;
    }

    public function logout(): void
    {
        Session::destroy();
        header('Location: ' . BASE_URL . 'index.php?page=login_student');
        exit;
    }

    /** POST: Change password (works for both roles) */
    public function changePassword(): void
    {
        if (!Session::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'index.php?page=login_student');
            exit;
        }

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        $userId = (int)Session::get('user_id');
        $user = $this->userModel->findById($userId);
        $redirect = Session::role() === 'admin' ? 'admin_change_password' : 'student_change_password';

        if (!$user || !password_verify($current, $user['password'])) {
            Session::setFlash('error', 'Current password is incorrect.');
            header('Location: ' . BASE_URL . 'index.php?page=' . $redirect);
            exit;
        }
        if (!Validator::isStrongPassword($new) || $new !== $confirm) {
            Session::setFlash('error', 'New password must be at least 6 characters and match confirmation.');
            header('Location: ' . BASE_URL . 'index.php?page=' . $redirect);
            exit;
        }

        $this->userModel->updatePassword($userId, password_hash($new, PASSWORD_BCRYPT));
        Session::setFlash('success', 'Password updated successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=' . $redirect);
        exit;
    }
}
