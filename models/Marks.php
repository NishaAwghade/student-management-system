<?php
/**
 * Marks Model
 * Handles student exam/performance records.
 */
class Marks
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function add(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO marks (student_id, subject, exam_type, marks_obtained, total_marks, exam_date, added_by)
             VALUES (:sid, :subject, :exam_type, :obtained, :total, :date, :by)'
        );
        $stmt->execute([
            'sid'      => $data['student_id'],
            'subject'  => $data['subject'],
            'exam_type'=> $data['exam_type'],
            'obtained' => $data['marks_obtained'],
            'total'    => $data['total_marks'],
            'date'     => $data['exam_date'] ?: null,
            'by'       => $data['added_by'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE marks SET subject = :subject, exam_type = :exam_type,
             marks_obtained = :obtained, total_marks = :total, exam_date = :date
             WHERE id = :id'
        );
        return $stmt->execute([
            'subject'  => $data['subject'],
            'exam_type'=> $data['exam_type'],
            'obtained' => $data['marks_obtained'],
            'total'    => $data['total_marks'],
            'date'     => $data['exam_date'] ?: null,
            'id'       => $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM marks WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM marks WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByStudent(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM marks WHERE student_id = :sid ORDER BY exam_date DESC');
        $stmt->execute(['sid' => $studentId]);
        return $stmt->fetchAll();
    }

    /** Average percentage per subject, used for simple performance charts. */
    public function getSubjectAverages(int $studentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT subject, ROUND(AVG(marks_obtained / total_marks * 100), 1) AS avg_pct
             FROM marks WHERE student_id = :sid GROUP BY subject'
        );
        $stmt->execute(['sid' => $studentId]);
        return $stmt->fetchAll();
    }
}
