<?php

namespace ksoftm\system\utils;


/**
 * session class
 */
class Session
{
    /**
     * Class constructor.
     */
    protected function __construct($timestamp, $path, $domain, $secure, $httpOnly)
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            session_set_cookie_params(
                $timestamp,
                $path,
                $domain ?: session_get_cookie_params()['domain'],
                $secure,
                $httpOnly
            );
            session_start();
            session_regenerate_id(true);
        }
    }

    public static function new(
        int $timestamp = 0,
        string $path = '/',
        string $domain = null,
        bool $secure = true,
        bool $httpOnly = true
    ): Session {
        return new Session($timestamp, $path, $domain, $secure, $httpOnly);
    }

    public function flash(string $id, string $message)
    {
        $_SESSION[$id] = $message;
        session_commit();
    }

    public function haveKey(string $key): bool
    {
        if (array_key_exists($key, $_SESSION)) {
            return true;
        }
        return false;
    }

    public function haveValue(mixed $value): bool
    {
        if (in_array($value, $_SESSION)) {
            return true;
        }
        return false;
    }

    protected function getKey(mixed $value): string|false
    {
        foreach ($_SESSION as $k => $v) {
            if ($v === $value) {
                return $k;
            }
        }
        return false;
    }

    public function removeByKey(string $key)
    {
        if ($this->haveKey($key)) {
            unset($_SESSION[$key]);
        }
        session_commit();
    }

    public function removeByValue(string $value)
    {
        if ($this->haveValue($value)) {
            unset($_SESSION[$this->getKey($value)]);
        }
        session_commit();
    }

    public function clean()
    {
        session_destroy();
    }

    public function getByKey(string $key, string $default = ''): mixed
    {
        if ($this->haveKey($key)) {
            $d = $_SESSION[$key];
        }
        session_commit();
        session_regenerate_id(true);
        return $d ?? $default;
    }

    public function getOnceByKey(string $key, string $default = ''): mixed
    {
        $d = $this->getByKey($key, $default);
        $this->removeByKey($key);
        return $d ?? $default;
    }
}
