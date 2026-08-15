<?php
namespace App\Core;

class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(self::token()) . '">';
    }

    public static function verify(?string $token): bool
    {
        if (empty($_SESSION[self::SESSION_KEY]) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION[self::SESSION_KEY], $token);
    }

    /**
     * Reads the submitted token from a form field, an X-CSRF-Token header,
     * or a JSON request body - covers plain form posts and the app's AJAX
     * endpoints (which send JSON bodies, including for PUT/DELETE).
     */
    public static function fromRequest(): ?string
    {
        if (!empty($_POST['_csrf'])) {
            return $_POST['_csrf'];
        }

        $header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!empty($header)) {
            return $header;
        }

        $raw = file_get_contents('php://input');
        if ($raw) {
            $data = json_decode($raw, true);
            if (is_array($data) && !empty($data['_csrf'])) {
                return $data['_csrf'];
            }
        }

        return null;
    }
}
