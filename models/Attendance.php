<?php
/**
 * Attendance Model
 */
class Attendance
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** Insert or update (upsert) attendance for one student on one date. */
    public function mark(int $studentId, string $date, string $status, int $markedBy): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO attendance (student_id, date, status, marked_by)
             VALUES (:sid, :date, :status, :by)
             ON DUPLICATE KEY UPDATE status = VALUES(status), marked_by = VALUES(marked_by)'
        );
        return $stmt->execute([
            'sid' => $studentId, 'date' => $date, 'status' => $status, 'by' => $markedBy,
        ]);
    }

    public function getByStudent(int $studentId, int $limit = 90): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM attendance WHERE student_id = :sid ORDER BY date DESC LIMIT :lim'
        );
        $stmt->bindValue('sid', $studentId, PDO::PARAM_INT);
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByClassAndDate(string $class, string $date): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.id AS student_id, u.name, s.roll_no, a.status
             FROM students s
             JOIN users u ON u.id = s.user_id
             LEFT JOIN attendance a ON a.student_id = s.id AND a.date = :date
             WHERE s.class = :class
             ORDER BY u.name'
        );
        $stmt->execute(['date' => $date, 'class' => $class]);
        return $stmt->fetchAll();
    }

    /** Summary stats used on the student dashboard / reports. */
    public function getSummary(int $studentId): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                SUM(status='Present') AS present_count,
                SUM(status='Absent')  AS absent_count,
                SUM(status='Leave')   AS leave_count,
                COUNT(*) AS total
             FROM attendance WHERE student_id = :sid"
        );
        $stmt->execute(['sid' => $studentId]);
        $row = $stmt->fetch();
        $total = (int)($row['total'] ?? 0);
        $present = (int)($row['present_count'] ?? 0);
        $row['percentage'] = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        return $row;
    }
}
