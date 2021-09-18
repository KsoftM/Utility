<?php

namespace ksoftm\system\utils;

class Cookie
{
    protected string $encryptKey;
    protected string $name;
    protected mixed $value;
    protected int $minutes = 10;
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
        int $minutes = 10,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httpOnly = true
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->addMinutes($minutes);
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }

    public static function make(
        string $name,
        mixed $value = '',
        int $minutes = 10,
        string $path = '/',
        string $domain = '',
        bool $secure = true,
        bool $httpOnly = true
    ): Cookie|false {

        return new Cookie(
            $name,
            $value,
            $minutes,
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

    // add more minutes for the cookie
    public function addMinutes(int $minutes): Cookie
    {
        $this->minutes = time() + (60 * $minutes);
        return $this;
    }

    // get the encrypted key
    public function getEncryptKey(): string
    {
        return $this->encryptKey;
    }

    // this is a fixed hashing method
    // never reload the hash for a set of key & value => need to fix
    //TODO encryption bug must be fixed
    public function encrypted(): Cookie|false
    {
        $this->encryptKey = EndeCorder::generateUniqueKey();

        $this->value = base64_encode(EndeCorder::new($this->encryptKey)->SSLEncrypt($this->value));

        return $this;
    }

    // serialized the value if the user want
    public function serialized(): Cookie|false
    {
        $this->value = serialize($this->value);
        return $this;
    }

    public function start(): Cookie|false
    {
        setcookie(
            $this->name,
            $this->value,
            $this->minutes,
            $this->path,
            $this->domain,
            $this->secure,
            $this->httpOnly
        );
        return $this;
    }

    public function startWidthTime(float $minutes = 10): Cookie
    {
        $this->addMinutes($minutes);
        return $this->start();
    }

    public function end(): Cookie|false
    {
        if (!empty($this->name))
            setcookie($this->name, '',  time() - 9999);

        return $this;
    }

    //TODO DECRYPTION BUG MUST BE FIXED
    public function get(string $name, string $default = ''): mixed
    {
        $this->value = filter_input(INPUT_COOKIE, $name, FILTER_SANITIZE_SPECIAL_CHARS);
        // $this->value = base64_decode(filter_var($_COOKIE[$name], FILTER_SANITIZE_STRING));

        echo '<pre>';
        var_dump('get : ' . $this->value);
        echo '</pre>';
        // exit;

        if (isset($this->value)) {
            $this->value = EndeCorder::new($this->encryptKey)->SSLDecrypt($this->value);
            return $this->value ?? $default;
        }

        return $default;
    }
}
