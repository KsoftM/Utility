<?php

namespace ksoftm\system\utils;

use Exception;

class EndeCorder
{

    //<<----------->> aloud cipher and it's length <<----------->>//

    public const CIPHER_AES_128_CTR = 'AES-128-CTR';
    public const CIPHER_AES_256_CTR = 'AES-256-CTR';

    protected const MAX_LENGTH_AES_128_CTR = 16;
    protected const MAX_LENGTH_AES_256_CTR = 32;

    //<<-----X----->> aloud cipher <<-----X----->>//


    /**
     * encryption key
     *
     * @var string
     */
    protected string $key;

    /**
     * cipher method for encryption
     *
     * @var string
     */
    protected string $cipher;


    /**
     * encryption class
     *
     * @param string $key length between 32 and 64
     * @param string $cipher
     */
    protected function __construct(string $key, string $cipher)
    {
        if (self::checkValidCipher($key, $cipher)) {
            $this->key = $key;
            $this->cipher = $cipher;
        } else {
            throw new Exception("Invalid key and cipher.");
        }
    }

    /**
     * create new EndeCorder
     *
     * @param string $key
     * @param [type] $cipher
     *
     * @return EndeCorder
     */
    public static function new(string $key, string $cipher = EndeCorder::CIPHER_AES_128_CTR): EndeCorder
    {
        return new EndeCorder($key, $cipher);
    }

    /**
     * check the key and cipher is valid
     *
     * @param string $key
     * @param string $cipher
     *
     * @return boolean
     */
    public static function checkValidCipher(string $key, string $cipher): bool
    {
        $bitCount = mb_strlen($key, '8bit');

        return ($bitCount == EndeCorder::MAX_LENGTH_AES_128_CTR &&
            $cipher === EndeCorder::CIPHER_AES_128_CTR) ||
            ($bitCount == EndeCorder::MAX_LENGTH_AES_256_CTR &&
                $cipher === EndeCorder::CIPHER_AES_256_CTR);
    }


    //<<----------->> generate unique key <<----------->>//

    public static function generateUniqueKey(
        string $cipher = EndeCorder::CIPHER_AES_128_CTR
    ): string {
        return openssl_random_pseudo_bytes(
            $cipher === EndeCorder::CIPHER_AES_128_CTR ?
                EndeCorder::MAX_LENGTH_AES_128_CTR :
                EndeCorder::MAX_LENGTH_AES_256_CTR
        );
    }

    //<<-----X----->> generate unique key <<-----X----->>//


    //<<----------->> encryption and decryption <<----------->>//

    /**
     * encrypt plane text into chipper text
     *
     * @param string $data plane text
     * @param string $key key to encrypt and decrypt tha data
     * @param string $algorithm base algorithm for encryption and decryption
     *
     * @return string
     */
    public function SSLEncrypt(mixed $data, bool $serialization = false): string
    {
        // create the unique in for specified algorithm
        $iv = bin2hex(openssl_random_pseudo_bytes(
            openssl_cipher_iv_length($this->cipher) / 2
        ));

        // create a raw encrypted data
        $data =  openssl_encrypt(
            $serialization ? serialize($data) : $data,
            $this->cipher,
            $this->key,
            0,
            $iv
        );

        $iv = base64_encode($iv);

        $hash = json_encode(compact('iv', 'data'));

        if ($hash == false || $data == false) {
            throw new Exception('SSL Encryption failed.');
        }

        return base64_encode($hash);
    }

    /**
     * decrypt the encrypted text into plane text
     *
     * @param string $encryptData encrypted text
     * @param string $key
     * @param string $algorithm
     *
     * @return string
     */
    public function SSLDecrypt(
        string $data,
        bool $serialization = false
    ): string {

        // separate the [encryptedData] and [iv] from base64 formatted encrypted data
        ['iv' => $iv, 'data' => $data] = json_decode(base64_decode($data), true);

        // decrypt the data
        $data = openssl_decrypt(
            $data,
            $this->cipher,
            $this->key,
            0,
            base64_decode($iv)
        );

        if ($data == false) {
            throw new Exception('SSL Decryption failed.');
        }

        return $serialization ? unserialize($data) : $data;
    }

    //<<-----X----->> encryption and decryption <<-----X----->>//



    //<<----------->> big hashing with key <<----------->>//

    /**
     * make a big hash data wih key
     *
     * @param string $data
     * @param boolean $binary
     * @param string $method
     *
     * @return string
     */
    public static function BigHash(string $data, string $key, bool $binary = false, string $method = 'sha256'): string
    {
        return hash_hmac($method, $data, $key, $binary);
    }

    //<<-----X----->> big hashing with key <<-----X----->>//


    //<<----------->> token hashing and token validation <<----------->>//

    /**
     * make a validation token
     *
     * @param string $nameOfTheToken
     * @param string $uniqueKey
     * @param boolean $validTimeInSecond
     *
     * @return string
     */
    public static function Token(
        string $name,
        string $uniqueKey,
        int|false $validTimeInSecond = false
    ): string {
        // check the time validation is available
        // make the time validation if it is available
        if ($validTimeInSecond != false) {
            $time = base64_encode((time() + $validTimeInSecond));
            $name = json_encode(compact('name', 'time'), JSON_UNESCAPED_SLASHES);
        } else {
            $name = json_encode(compact('name'), JSON_UNESCAPED_SLASHES);
        }

        // return the encrypted key
        return EndeCorder::new($uniqueKey)->SSLEncrypt($name);
    }

    /**
     * check the token is valid or not and get the message.
     *
     * @param string $token
     * @param string $nameOfTheToken
     * @param string $uniqueKey
     *
     * @return TokenResult
     */
    public static function TokenValidate(
        string $name,
        string $token,
        string $uniqueKey
    ): TokenResult {
        // decrypt the data and check it is a valid token
        $token = EndeCorder::new($uniqueKey)->SSLDecrypt($token);

        if (!empty($token)) {
            // separate the name and time
            $token = (array) json_decode($token);

            // to reduce the null indexing exception
            [
                'name' => $name,
                'time' => $time
            ] = $token + ['name' => null, 'time' => null];

            if (!empty($name) && $name === $name) {
                if (!empty($time)) {
                    $time = base64_decode($time);
                    // time validation check
                    if (($time - time()) > 0) {
                        // valid token with time validation
                        return new TokenResult(true, 'Token is validated successfully.');
                    } else {
                        return new TokenResult(false, 'Token is expired!');
                    }
                } else {
                    // valid token without time validation
                    return new TokenResult(true, 'Token is validated successfully.');
                }
            } else {
                return new TokenResult(false, 'Token name is not valid!');
            }
        } else {
            return new TokenResult(false, 'Token is not valid!');
        }

        return new TokenResult(false, 'Unknown errors founded!');
    }

    //<<-----X----->> token hashing and token validation <<-----X----->>//


    //<<----------->> password hashing methods <<----------->>//

    /**
     * create a hash password.
     *
     * @param string $password
     * @param integer $cost
     *
     * @return string
     */
    public function HashedPassword(string $password, int $cost = 8): string
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => $cost]);
    }

    /**
     * check the password hash matches
     *
     * @param string $password
     * @param string $hash
     *
     * @return boolean
     */
    public function VerifyHashedPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    //<<-----X----->> password hashing methods <<-----X----->>//
}
