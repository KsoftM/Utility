<?php

namespace ksoftm\system\utils;


/**
 * session class
 */
class Session
{
    protected const KEY_OF_SESSION = 'flash_session';
    
    /**
     * Class constructor.
     */
    protected function __construct($timestamp, $path, $domain, $secure, $httpOnly)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_set_cookie_params($timestamp, $path, $domain, $secure, $httpOnly);
            session_start();
        }
    }

    public static function new(
        int $timestamp = 0,
        string $path = '/',
        string $domain = 'localhost',
        bool $secure = true,
        bool $httpOnly = true
    ): Session {
        return new Session($timestamp, $path, $domain, $secure, $httpOnly);
    }

    public static function flash(
        string $id,
        string $message,
        int $timestamp = 0
    ) {
        Session::new($timestamp);
        session_regenerate_id(true);
        $_SESSION[Session::KEY_OF_SESSION][$id] = $message;
    }

    public static function have(string $id): bool
    {
        if (!empty($_SESSION[Session::KEY_OF_SESSION][$id])) {
            return true;
        }
        return false;
    }

    public static function remove(string $id)
    {
        if (self::have($id)) {
            unset($_SESSION[Session::KEY_OF_SESSION][$id]);
        }
        session_regenerate_id(true);
    }

    public static function removeOnce(string $id)
    {
        if (self::have($id)) {
            unset($_SESSION[Session::KEY_OF_SESSION][$id]);
            sort($_SESSION[Session::KEY_OF_SESSION]);
            session_regenerate_id(true);
        }
    }

    public static function clean()
    {
        session_destroy();
        session_regenerate_id(true);
    }

    public static function get(string $id, string $default = ''): mixed
    {
        Session::new();
        if (self::have($id)) {
            $d = $_SESSION[Session::KEY_OF_SESSION][$id];
        }
        session_regenerate_id(true);
        return $d ?? $default;
    }

    public static function getOnce(string $id, string $default = ''): mixed
    {
        Session::new();
        $d = Session::get($id, $default);
        self::remove($id);
        return $d ?? $default;
    }
}
