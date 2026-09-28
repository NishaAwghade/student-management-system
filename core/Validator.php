<?php
/**
 * Validator
 * Lightweight, reusable server-side input validation & sanitisation helpers.
 * Always validate on the server even if client-side (JS/Bootstrap) validation exists,
 * since client-side checks can be bypassed.
 */
class Validator
{
    public static function sanitize(?string $value): string
    {
        return htmlspecialchars(trim($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    public static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function isRequired($value): bool
    {
        return $value !== null && trim((string)$value) !== '';
    }

    public static function minLength(string $value, int $len): bool
    {
        return mb_strlen($value) >= $len;
    }

    /** Strong-ish password rule: min 6 chars (relax/tighten as needed). */
    public static function isStrongPassword(string $value): bool
    {
        return mb_strlen($value) >= 6;
    }

    public static function isNumeric($value): bool
    {
        return is_numeric($value);
    }

    public static function isDate(string $value): bool
    {
        $d = DateTime::createFromFormat('Y-m-d', $value);
        return $d && $d->format('Y-m-d') === $value;
    }

    /** Validate uploaded file extension against the allowed list. */
    public static function isAllowedFile(string $filename): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ALLOWED_EXTENSIONS, true);
    }
}
