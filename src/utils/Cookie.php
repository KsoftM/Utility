<?php

namespace ksoftm\system\utils;

class Cookie
{
    protected string $encryptKey;
    protected string $name;
    protected mixed $value;
    protected int $timestamp = 3600;
    protected string $path = '/';
    protected string $domain = '';
    protected bool $secure = true;
    protected bool $httpOnly = true;


    // reference for the cookie
    // $request->cookie('key');



    /**
     * Class constructor.
     */
    protected function __construct(
        string $name,
        mixed $value,
        int $timestamp,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httpOnly = true
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->timestamp = $timestamp;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    public static function make(
        string $name,
        mixed $value = '',
        int $timestamp = 3600,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httpOnly = true
    ): Cookie|false {

        return new Cookie(
            $name,
            $value,
            $timestamp,
            $path,
            $domain,
            $secure,
            $httpOnly
        );
    }

    // getter and setter for name field
    public function name(string $name = null): Cookie|string
    {
        if (empty($name)) return $this->name;

        $this->name = $name;

        return $this;
    }

    // getter and setter for the encrypted key
    public function EncryptKey(string $key = null): string
    {
        if (!empty($key)) {
            $this->encryptKey = $key;
        }
        return $this->encryptKey;
    }

    public function encrypt(string $key): Cookie|false
    {
        $this->value = EndeCorder::new($this->EncryptKey($key))->SSLEncrypt($this->value);

        return $this;
    }

    // serialized the value if the user want
    public function serialized(): Cookie|false
    {
        $this->value = serialize($this->value);
        return $this;
    }

    /**
     * start the cookie
     *
     * @return Cookie|false
     */
    public function start(): Cookie|false
    {
        setcookie(
            $this->name,
            $this->value,
            $this->timestamp,
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
        );
        return $this;
    }

    public function startWidthTime(float $timestamp = 3600): Cookie
    {
        $this->timestamp = $timestamp;
        return $this->start();
    }

    /**
     * remove the cookie in the browser
     *
     * @return Cookie|false
     */
    public function end(): Cookie|false
    {
        if (!empty($this->name))
            setcookie($this->name, '',  time() - 9999);

        return $this;
    }

    /**
     * get the named cookie, return $default if the cookie is not exist
     *
     * @param string $default
     *
     * @return mixed
     */
    public function get(string $default = ''): mixed
    {
        $this->value = filter_input(INPUT_COOKIE, $this->name, FILTER_SANITIZE_SPECIAL_CHARS);

        return $this->value ?? $default;
    }

    /**
     * get the encrypted cookie and decrypt that.
     *
     * @param string $key
     * @param string $default
     *
     * @return mixed
     */
    public function getEncrypted(string $key, string $default = ''): mixed
    {
        $this->get();

        if (isset($this->value)) {
            $this->value = EndeCorder::new($this->EncryptKey($key))->SSLDecrypt($this->value);
            return $this->value ?? $default;
        }

        return $default;
    }
}
