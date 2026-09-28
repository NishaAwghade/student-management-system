<?php
/**
 * User Model
 * Handles the `users` table: authentication, registration, password changes.
 */
class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Register a new student user. Returns the new user's id. */
    public function createStudentUser(string $name, string $email, string $passwordHash): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, "student")'
        );
        $stmt->execute([
            'name'     => $name,
            'email'    => $email,
            'password' => $passwordHash,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function updatePassword(int $userId, string $newHash): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :password WHERE id = :id');
        return $stmt->execute(['password' => $newHash, 'id' => $userId]);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        return (bool)$stmt->fetch();
    }
}
