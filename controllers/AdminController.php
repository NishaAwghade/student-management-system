<?php
/**
 * AdminController
 * All actions available to logged-in admins/teachers:
 * manage students, attendance, marks, assignments, notes,
 * submission review, announcements.
 */
class AdminController
{
    private Student $studentModel;
    private Attendance $attendanceModel;
    private Marks $marksModel;
    private Assignment $assignmentModel;
    private Notes $notesModel;
    private Announcement $announcementModel;
    private Notification $notificationModel;
    private User $userModel;

    public function __construct()
    {
        Session::requireRole('admin');
        $this->studentModel = new Student();
        $this->attendanceModel = new Attendance();
        $this->marksModel = new Marks();
        $this->assignmentModel = new Assignment();
        $this->notesModel = new Notes();
        $this->announcementModel = new Announcement();
        $this->notificationModel = new Notification();
        $this->userModel = new User();
    }

    // ---------------- Manage Students ----------------

    public function addStudent(): void
    {
        $name    = Validator::sanitize($_POST['name'] ?? '');
        $email   = Validator::sanitize($_POST['email'] ?? '');
        $rollNo  = Validator::sanitize($_POST['roll_no'] ?? '');
        $class   = Validator::sanitize($_POST['class'] ?? '');
        $section = Validator::sanitize($_POST['section'] ?? '');
        $phone   = Validator::sanitize($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? 'Student@123'; // default temp password

        $errors = [];
        if (!Validator::isRequired($name)) $errors[] = 'Name is required.';
        if (!Validator::isEmail($email)) $errors[] = 'Valid email is required.';
        if (!Validator::isRequired($rollNo)) $errors[] = 'Roll number is required.';
        if (!Validator::isRequired($class)) $errors[] = 'Class is required.';
        if ($this->userModel->emailExists($email)) $errors[] = 'Email already exists.';

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            header('Location: ' . BASE_URL . 'index.php?page=admin_manage_students');
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $userId = $this->userModel->createStudentUser($name, $email, $hash);
        $this->studentModel->create([
            'user_id' => $userId, 'roll_no' => $rollNo, 'class' => $class,
            'section' => $section, 'phone' => $phone,
        ]);

        Session::setFlash('success', "Student added. Temporary password: $password");
        header('Location: ' . BASE_URL . 'index.php?page=admin_manage_students');
        exit;
    }

    public function editStudent(): void
    {
        $id = (int)($_POST['student_id'] ?? 0);
        $this->studentModel->update($id, [
            'class'   => Validator::sanitize($_POST['class'] ?? ''),
            'section' => Validator::sanitize($_POST['section'] ?? ''),
            'phone'   => Validator::sanitize($_POST['phone'] ?? ''),
            'address' => Validator::sanitize($_POST['address'] ?? ''),
            'dob'     => Validator::sanitize($_POST['dob'] ?? ''),
            'gender'  => Validator::sanitize($_POST['gender'] ?? ''),
        ]);
        Session::setFlash('success', 'Student updated successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_manage_students');
        exit;
    }

    public function deleteStudent(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->studentModel->delete($id);
        Session::setFlash('success', 'Student removed successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_manage_students');
        exit;
    }

    // ---------------- Attendance ----------------

    public function markAttendance(): void
    {
        $adminId = (int)Session::get('user_id');
        $date = Validator::sanitize($_POST['date'] ?? date('Y-m-d'));
        $statuses = $_POST['status'] ?? []; // [student_id => status]

        if (!Validator::isDate($date)) {
            Session::setFlash('error', 'Invalid date.');
            header('Location: ' . BASE_URL . 'index.php?page=admin_attendance');
            exit;
        }

        foreach ($statuses as $studentId => $status) {
            if (in_array($status, ['Present', 'Absent', 'Leave'], true)) {
                $this->attendanceModel->mark((int)$studentId, $date, $status, $adminId);
            }
        }

        Session::setFlash('success', 'Attendance saved for ' . $date . '.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_attendance');
        exit;
    }

    // ---------------- Marks / Performance ----------------

    public function addMarks(): void
    {
        $studentId = (int)($_POST['student_id'] ?? 0);
        $this->marksModel->add([
            'student_id'     => $studentId,
            'subject'        => Validator::sanitize($_POST['subject'] ?? ''),
            'exam_type'      => Validator::sanitize($_POST['exam_type'] ?? ''),
            'marks_obtained' => (float)($_POST['marks_obtained'] ?? 0),
            'total_marks'    => (float)($_POST['total_marks'] ?? 100),
            'exam_date'      => Validator::sanitize($_POST['exam_date'] ?? ''),
            'added_by'       => (int)Session::get('user_id'),
        ]);

        $student = $this->studentModel->findById($studentId);
        if ($student) {
            $this->notificationModel->create((int)$student['user_id'], 'New marks have been published for you.');
        }

        Session::setFlash('success', 'Marks added successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_marks&student_id=' . $studentId);
        exit;
    }

    public function editMarks(): void
    {
        $id = (int)($_POST['mark_id'] ?? 0);
        $studentId = (int)($_POST['student_id'] ?? 0);
        $this->marksModel->update($id, [
            'subject'        => Validator::sanitize($_POST['subject'] ?? ''),
            'exam_type'      => Validator::sanitize($_POST['exam_type'] ?? ''),
            'marks_obtained' => (float)($_POST['marks_obtained'] ?? 0),
            'total_marks'    => (float)($_POST['total_marks'] ?? 100),
            'exam_date'      => Validator::sanitize($_POST['exam_date'] ?? ''),
        ]);
        Session::setFlash('success', 'Marks updated successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_marks&student_id=' . $studentId);
        exit;
    }

    public function deleteMarks(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $studentId = (int)($_GET['student_id'] ?? 0);
        $this->marksModel->delete($id);
        Session::setFlash('success', 'Marks record deleted.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_marks&student_id=' . $studentId);
        exit;
    }

    // ---------------- Assignments ----------------

    public function createAssignment(): void
    {
        $filePath = null;
        if (!empty($_FILES['file']['name'])) {
            $file = $_FILES['file'];
            if (Validator::isAllowedFile($file['name']) && $file['size'] <= MAX_UPLOAD_SIZE && $file['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $filePath = 'assign_' . time() . '.' . $ext;
                move_uploaded_file($file['tmp_name'], UPLOAD_ASSIGNMENTS . $filePath);
            }
        }

        $this->assignmentModel->create([
            'title'       => Validator::sanitize($_POST['title'] ?? ''),
            'description' => Validator::sanitize($_POST['description'] ?? ''),
            'subject'     => Validator::sanitize($_POST['subject'] ?? ''),
            'class'       => Validator::sanitize($_POST['class'] ?? ''),
            'due_date'    => Validator::sanitize($_POST['due_date'] ?? ''),
            'file_path'   => $filePath,
            'uploaded_by' => (int)Session::get('user_id'),
        ]);

        Session::setFlash('success', 'Assignment created successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_assignments');
        exit;
    }

    public function reviewSubmission(): void
    {
        $submissionId = (int)($_POST['submission_id'] ?? 0);
        $assignmentId = (int)($_POST['assignment_id'] ?? 0);
        $status = Validator::sanitize($_POST['status'] ?? '');
        $remarks = Validator::sanitize($_POST['remarks'] ?? '');

        if (in_array($status, ['Approved', 'Rejected'], true)) {
            $this->assignmentModel->reviewSubmission($submissionId, $status, $remarks);
        }

        Session::setFlash('success', 'Submission reviewed.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_view_submissions&assignment_id=' . $assignmentId);
        exit;
    }

    // ---------------- Notes ----------------

    public function uploadNotes(): void
    {
        if (empty($_FILES['file']['name'])) {
            Session::setFlash('error', 'Please choose a file to upload.');
            header('Location: ' . BASE_URL . 'index.php?page=admin_notes');
            exit;
        }

        $file = $_FILES['file'];
        if (!Validator::isAllowedFile($file['name']) || $file['size'] > MAX_UPLOAD_SIZE || $file['error'] !== UPLOAD_ERR_OK) {
            Session::setFlash('error', 'Invalid file type or size.');
            header('Location: ' . BASE_URL . 'index.php?page=admin_notes');
            exit;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $newName = 'note_' . time() . '.' . $ext;
        move_uploaded_file($file['tmp_name'], UPLOAD_NOTES . $newName);

        $this->notesModel->create([
            'title'       => Validator::sanitize($_POST['title'] ?? ''),
            'subject'     => Validator::sanitize($_POST['subject'] ?? ''),
            'class'       => Validator::sanitize($_POST['class'] ?? ''),
            'file_path'   => $newName,
            'uploaded_by' => (int)Session::get('user_id'),
        ]);

        Session::setFlash('success', 'Notes uploaded successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_notes');
        exit;
    }

    public function deleteNotes(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->notesModel->delete($id);
        Session::setFlash('success', 'Notes deleted.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_notes');
        exit;
    }

    // ---------------- Announcements ----------------

    public function createAnnouncement(): void
    {
        $title = Validator::sanitize($_POST['title'] ?? '');
        $message = Validator::sanitize($_POST['message'] ?? '');

        if (!Validator::isRequired($title) || !Validator::isRequired($message)) {
            Session::setFlash('error', 'Title and message are required.');
            header('Location: ' . BASE_URL . 'index.php?page=admin_announcements');
            exit;
        }

        $adminId = (int)Session::get('user_id');
        $this->announcementModel->create($title, $message, $adminId);

        // Notify all students
        foreach ($this->studentModel->getAll() as $s) {
            $this->notificationModel->create((int)$s['user_id'], "New announcement: $title");
        }

        Session::setFlash('success', 'Announcement posted successfully.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_announcements');
        exit;
    }

    public function deleteAnnouncement(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->announcementModel->delete($id);
        Session::setFlash('success', 'Announcement removed.');
        header('Location: ' . BASE_URL . 'index.php?page=admin_announcements');
        exit;
    }
}
