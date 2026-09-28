<?php
/**
 * Announcement Model
 */
class Announcement
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(string $title, string $message, int $createdBy): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO announcements (title, message, created_by) VALUES (:title, :message, :by)'
        );
        $stmt->execute(['title' => $title, 'message' => $message, 'by' => $createdBy]);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.name AS author FROM announcements a
             JOIN users u ON u.id = a.created_by ORDER BY a.created_at DESC LIMIT :lim'
        );
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM announcements WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}
