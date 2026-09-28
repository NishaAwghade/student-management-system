<?php
/**
 * Notification Model
 * Per-user notifications (e.g. new marks published, assignment reviewed).
 */
class Notification
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(int $userId, string $message): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notifications (user_id, message) VALUES (:uid, :msg)'
        );
        return $stmt->execute(['uid' => $userId, 'msg' => $message]);
    }

    public function getForUser(int $userId, int $limit = 30): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim'
        );
        $stmt->bindValue('uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) c FROM notifications WHERE user_id = :uid AND is_read = 0'
        );
        $stmt->execute(['uid' => $userId]);
        return (int)$stmt->fetch()['c'];
    }

    public function markAllRead(int $userId): bool
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = :uid');
        return $stmt->execute(['uid' => $userId]);
    }
}
