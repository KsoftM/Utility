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
        string $tag = null,
        int $timestamp = 0
    ) {
        Session::new($timestamp);
        session_regenerate_id(true);
        if (!empty($tag)) {
            $_SESSION[Session::KEY_OF_SESSION][$id][$tag] = $message;
        } else {
            $_SESSION[Session::KEY_OF_SESSION][$id][] = $message;
        }
    }

    public static function have(string $id, string $tag = null): bool
    {
        if (!empty($tag) && !empty($_SESSION[Session::KEY_OF_SESSION][$id][$tag])) {
            $out = true;
        } elseif (empty($tag) && !empty($_SESSION[Session::KEY_OF_SESSION][$id])) {
            $out = true;
        }
        return $out ?? false;
    }

    public static function remove(string $id)
    {
        if (self::have($id)) {
            unset($_SESSION[Session::KEY_OF_SESSION][$id]);
        }
        session_regenerate_id(true);
    }

    public static function removeOnce(string $id, mixed $tag)
    {
        if (self::have($id, $tag)) {
            unset($_SESSION[Session::KEY_OF_SESSION][$id][$tag]);
            sort($_SESSION[Session::KEY_OF_SESSION][$id]);
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

    public static function getFirst(string $id, string $tag = null, string $default = ''): mixed
    {
        Session::new();

        $tag = !empty($tag) ? $tag : 0;
        $d = (isset(Session::get($id, $default)[$tag]))
            ? Session::get($id, $default)[$tag]
            : $default;
        session_regenerate_id(true);

        return $d ?? $default;
    }

    public static function getFirstOnce(string $id, string $tag = null, string $default = ''): mixed
    {
        Session::new();
        
        $tag = !empty($tag) ? $tag : 0;
        $d = (isset(Session::get($id, $default)[$tag]))
        ? Session::get($id, $default)[$tag]
        : $default;
        self::removeOnce($id, $tag);
        session_regenerate_id(true);

        return $d ?? $default;
    }
}
