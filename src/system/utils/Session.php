<?php

namespace ksoftm\system\utils;


/**
 * session class
 *! Session class is under development
 */
class Session
{

    protected const KEY_OF_SESSION = 'flash_session';

    // return redirect()->action('HomeController@index', ['id' => 10]);

    /**
     * Class constructor.
     */
    protected function __construct($minutes, $path, $domain, $secure, $httpOnly)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_set_cookie_params(60 * $minutes, $path, $domain, $secure, $httpOnly);
            session_start();
        }
    }

    public static function new(
        int $minutes = 0,
        string $path = '/',
        string $domain = 'localhost',
        bool $secure = true,
        bool $httpOnly = true
    ): Session {
        return new Session($minutes, $path, $domain, $secure, $httpOnly);
    }

    public static function flash(string $tag, string $message, $minutes = 0)
    {
        Session::new($minutes);
        session_regenerate_id(true);
        $_SESSION[Session::KEY_OF_SESSION][$tag][] = $message;
    }

    public static function remove(string $tag)
    {
        session_regenerate_id(true);
        if (isset($_SESSION[Session::KEY_OF_SESSION][$tag])) {
            unset($_SESSION[Session::KEY_OF_SESSION][$tag]);
        }
    }

    public static function removeOnce(string $tag, mixed $key)
    {
        session_regenerate_id(true);
        if (isset($_SESSION[Session::KEY_OF_SESSION][$tag][$key])) {
            unset($_SESSION[Session::KEY_OF_SESSION][$tag][$key]);
        }
    }

    public static function clean()
    {
        session_regenerate_id(true);
        session_destroy();
    }

    public static function getOnce(string $tag, string $default = ''): mixed
    {
        Session::new();
        session_regenerate_id(true);
        if (isset($_SESSION[Session::KEY_OF_SESSION][$tag])) {
            $d = $_SESSION[Session::KEY_OF_SESSION][$tag];
        }
        self::remove($tag);
        return $d ?? $default;
    }

    public static function getFirstOnce(string $tag, string $default = ''): mixed
    {
        Session::new();
        session_regenerate_id(true);
        if (isset($_SESSION[Session::KEY_OF_SESSION][$tag])) {
            $d = $_SESSION[Session::KEY_OF_SESSION][$tag];
        }
        self::removeOnce($tag, 0);
        return $d[0] ?? $default;
    }
}
