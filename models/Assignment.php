<?php
/**
 * Assignment Model
 * Handles `assignments` (teacher-created tasks) and
 * `assignment_submissions` (student uploaded work + review status).
 */
class Assignment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // ---------------- Assignments (teacher side) ----------------

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO assignments (title, description, subject, class, file_path, due_date, uploaded_by)
             VALUES (:title, :description, :subject, :class, :file_path, :due_date, :by)'
        );
        $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'subject'     => $data['subject'],
            'class'       => $data['class'] ?: null,
            'file_path'   => $data['file_path'] ?? null,
            'due_date'    => $data['due_date'],
            'by'          => $data['uploaded_by'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT a.*, u.name AS teacher_name FROM assignments a
             JOIN users u ON u.id = a.uploaded_by ORDER BY a.due_date DESC'
        );
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM assignments WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Assignments visible to a given class, with this student's submission status attached. */
    public function getForStudent(int $studentId, string $class): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, s.status AS submission_status, s.file_path AS submission_file, s.id AS submission_id
             FROM assignments a
             LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.student_id = :sid
             WHERE a.class = :class OR a.class IS NULL
             ORDER BY a.due_date ASC'
        );
        $stmt->execute(['sid' => $studentId, 'class' => $class]);
        return $stmt->fetchAll();
    }

    // ---------------- Submissions (student side) ----------------

    public function submit(int $assignmentId, int $studentId, string $filePath): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO assignment_submissions (assignment_id, student_id, file_path, status)
             VALUES (:aid, :sid, :file, "Completed")
             ON DUPLICATE KEY UPDATE file_path = VALUES(file_path), status = "Completed", submitted_at = NOW()'
        );
        return $stmt->execute(['aid' => $assignmentId, 'sid' => $studentId, 'file' => $filePath]);
    }

    public function getSubmissionsForAssignment(int $assignmentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT sub.*, u.name, s.roll_no FROM assignment_submissions sub
             JOIN students s ON s.id = sub.student_id
             JOIN users u ON u.id = s.user_id
             WHERE sub.assignment_id = :aid ORDER BY sub.submitted_at DESC'
        );
        $stmt->execute(['aid' => $assignmentId]);
        return $stmt->fetchAll();
    }

    public function reviewSubmission(int $submissionId, string $status, string $remarks = ''): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE assignment_submissions SET status = :status, remarks = :remarks WHERE id = :id'
        );
        return $stmt->execute(['status' => $status, 'remarks' => $remarks, 'id' => $submissionId]);
    }

    public function countPendingForStudent(int $studentId, string $class): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) c FROM assignments a
             LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.student_id = :sid
             WHERE (a.class = :class OR a.class IS NULL) AND s.id IS NULL'
        );
        $stmt->execute(['sid' => $studentId, 'class' => $class]);
        return (int)$stmt->fetch()['c'];
    }
}
