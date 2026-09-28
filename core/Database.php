<?php
/**
 * Database
 * Singleton PDO wrapper. Using PDO with prepared statements everywhere
 * in the app prevents SQL Injection (see all Model classes).
 */
require_once ROOT_PATH . '/config/database.php';

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // real prepared statements
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$instance;
    }
}
