<?php
/**
 * Student Model
 * Handles the `students` table plus joined queries with `users`.
 */
class Student
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO students (user_id, roll_no, class, section, phone, address, dob, gender, admission_date)
             VALUES (:user_id, :roll_no, :class, :section, :phone, :address, :dob, :gender, :admission_date)'
        );
        $stmt->execute([
            'user_id'        => $data['user_id'],
            'roll_no'        => $data['roll_no'],
            'class'          => $data['class'],
            'section'        => $data['section'] ?? null,
            'phone'          => $data['phone'] ?? null,
            'address'        => $data['address'] ?? null,
            'dob'            => $data['dob'] ?: null,
            'gender'         => $data['gender'] ?? null,
            'admission_date' => $data['admission_date'] ?: date('Y-m-d'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $studentId, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE students SET class = :class, section = :section, phone = :phone,
             address = :address, dob = :dob, gender = :gender
             WHERE id = :id'
        );
        return $stmt->execute([
            'class'   => $data['class'],
            'section' => $data['section'] ?? null,
            'phone'   => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'dob'     => $data['dob'] ?: null,
            'gender'  => $data['gender'] ?? null,
            'id'      => $studentId,
        ]);
    }

    public function updateProfilePic(int $studentId, string $filename): bool
    {
        $stmt = $this->db->prepare('UPDATE students SET profile_pic = :pic WHERE id = :id');
        return $stmt->execute(['pic' => $filename, 'id' => $studentId]);
    }

    public function delete(int $studentId): bool
    {
        // Deleting the linked user cascades to students + all related rows (FK ON DELETE CASCADE)
        $stmt = $this->db->prepare('SELECT user_id FROM students WHERE id = :id');
        $stmt->execute(['id' => $studentId]);
        $row = $stmt->fetch();
        if (!$row) return false;

        $del = $this->db->prepare('DELETE FROM users WHERE id = :uid');
        return $del->execute(['uid' => $row['user_id']]);
    }

    public function findById(int $studentId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.name, u.email, u.status FROM students s
             JOIN users u ON u.id = s.user_id WHERE s.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $studentId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.name, u.email, u.status FROM students s
             JOIN users u ON u.id = s.user_id WHERE s.user_id = :uid LIMIT 1'
        );
        $stmt->execute(['uid' => $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Get all students, optionally filtered by class/section/search keyword. */
    public function getAll(string $search = '', string $class = '', string $section = ''): array
    {
        $sql = 'SELECT s.*, u.name, u.email, u.status FROM students s
                JOIN users u ON u.id = s.user_id WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (u.name LIKE :search OR s.roll_no LIKE :search OR u.email LIKE :search)';
            $params['search'] = "%$search%";
        }
        if ($class !== '') {
            $sql .= ' AND s.class = :class';
            $params['class'] = $class;
        }
        if ($section !== '') {
            $sql .= ' AND s.section = :section';
            $params['section'] = $section;
        }
        $sql .= ' ORDER BY u.name ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) c FROM students')->fetch()['c'];
    }

    public function getDistinctClasses(): array
    {
        return $this->db->query('SELECT DISTINCT class FROM students ORDER BY class')->fetchAll(PDO::FETCH_COLUMN);
    }
}
