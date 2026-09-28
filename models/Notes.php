<?php
/**
 * Notes Model
 * Study material uploaded by teachers, downloadable by students.
 */
class Notes
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notes (title, subject, class, file_path, uploaded_by)
             VALUES (:title, :subject, :class, :file_path, :by)'
        );
        $stmt->execute([
            'title'     => $data['title'],
            'subject'   => $data['subject'],
            'class'     => $data['class'] ?: null,
            'file_path' => $data['file_path'],
            'by'        => $data['uploaded_by'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT n.*, u.name AS teacher_name FROM notes n
             JOIN users u ON u.id = n.uploaded_by ORDER BY n.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public function getForClass(string $class): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notes WHERE class = :class OR class IS NULL ORDER BY created_at DESC'
        );
        $stmt->execute(['class' => $class]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM notes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM notes WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
