<?php
/**
 * StudentController
 * All actions available to logged-in students: profile update,
 * assignment upload, notifications, etc.
 */
class StudentController
{
    private Student $studentModel;
    private Assignment $assignmentModel;
    private Notification $notificationModel;

    public function __construct()
    {
        Session::requireRole('student');
        $this->studentModel = new Student();
        $this->assignmentModel = new Assignment();
        $this->notificationModel = new Notification();
    }

    /** POST: update own profile info */
    public function updateProfile(): void
    {
        $studentId = (int)Session::get('student_id');
        $data = [
            'class'   => Validator::sanitize($_POST['class'] ?? ''),
            'section' => Validator::sanitize($_POST['section'] ?? ''),
            'phone'   => Validator::sanitize($_POST['phone'] ?? ''),
            'address' => Validator::sanitize($_POST['address'] ?? ''),
            'dob'     => Validator::sanitize($_POST['dob'] ?? ''),
            'gender'  => Validator::sanitize($_POST['gender'] ?? ''),
        ];

        if ($data['dob'] !== '' && !Validator::isDate($data['dob'])) {
            Session::setFlash('error', 'Invalid date of birth.');
            header('Location: ' . BASE_URL . 'index.php?page=student_profile');
            exit;
        }

        $this->studentModel->update($studentId, $data);
        Session::setFlash('success', 'Profile updated successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=student_profile');
        exit;
    }

    /** POST: upload/replace profile picture */
    public function uploadProfilePic(): void
    {
        $studentId = (int)Session::get('student_id');

        if (empty($_FILES['profile_pic']['name'])) {
            Session::setFlash('error', 'Please choose an image.');
            header('Location: ' . BASE_URL . 'index.php?page=student_profile');
            exit;
        }

        $file = $_FILES['profile_pic'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true) || $file['size'] > MAX_UPLOAD_SIZE || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Invalid image. Only JPG/PNG up to 5MB allowed.');
            header('Location: ' . BASE_URL . 'index.php?page=student_profile');
            exit;
        }

        $newName = 'profile_' . $studentId . '_' . time() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], UPLOAD_PROFILE . $newName);

        $this->studentModel->updateProfilePic($studentId, $newName);
        Session::setFlash('success', 'Profile picture updated.');
        header('Location: ' . BASE_URL . 'index.php?page=student_profile');
        exit;
    }

    /** POST: submit/upload an assignment */
    public function submitAssignment(): void
    {
        $studentId = (int)Session::get('student_id');
        $assignmentId = (int)($_POST['assignment_id'] ?? 0);

        if (!$assignmentId || empty($_FILES['submission_file']['name'])) {
            Session::setFlash('error', 'Please choose a file to submit.');
            header('Location: ' . BASE_URL . 'index.php?page=student_assignments');
            exit;
        }

        $file = $_FILES['submission_file'];
        if (!Validator::isAllowedFile($file['name']) || $file['size'] > MAX_UPLOAD_SIZE || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Invalid file. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS) . ' (max 5MB).');
            header('Location: ' . BASE_URL . 'index.php?page=student_assignments');
            exit;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newName = 'sub_' . $studentId . '_' . $assignmentId . '_' . time() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], UPLOAD_SUBMISSIONS . $newName);

        $this->assignmentModel->submit($assignmentId, $studentId, $newName);
        Session::setFlash('success', 'Assignment submitted successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=student_assignments');
        exit;
    }

    public function markNotificationsRead(): void
    {
        $this->notificationModel->markAllRead((int)Session::get('user_id'));
        header('Location: ' . BASE_URL . 'index.php?page=student_notifications');
        exit;
    }
}
