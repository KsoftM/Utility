<?php

namespace ksoftm\utils;

class EndeCorder
{
    protected const SEPARATOR = ' :: ';
    private const SELF_TOKEN_KEY = ' sdo+jke9/3w==';

    /**
     * encrypt plane text into chipper text
     *
     * @param string $data plane text
     * @param string $key key to encrypt and decrypt tha data
     * @param string $algorithm base algorithm for encryption and decryption
     *
     * @return string
     */
    public static function SSLEncryption(
        string $data,
        string $key,
        string $algorithm = 'AES-128-CTR'
    ): string {

        // decode tha key into base64 format
        $encryptKey = base64_decode($key);

        // create the unique in for specified algorithm
        $iv = bin2hex(random_bytes(
            openssl_cipher_iv_length($algorithm) / 2
        ));

        // create a raw encrypted data
        $encryptData =  openssl_encrypt(
            $data,
            $algorithm,
            $encryptKey,
            0,
            $iv
        );

        // join the raw [encryptedData] and [iv] and then format it into base64
        return base64_encode($encryptData . EndeCorder::SEPARATOR . $iv);
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
    public static function SSLDecryption(
        string $encryptData,
        string $key,
        string $algorithm = 'AES-128-CTR'
    ): string {
        // decode tha key into base64 format
        $encryptKey = base64_decode($key);

        // separate the [encryptedData] and [iv] from base64 formatted encrypted data
        [$encryptData, $iv] = explode(EndeCorder::SEPARATOR, base64_decode($encryptData), 2);

        // decrypt the data
        return openssl_decrypt(
            $encryptData,
            $algorithm,
            $encryptKey,
            0,
            $iv
        );
    }

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
        string $nameOfTheToken,
        string $uniqueKey = 'ksoftm',
        int|false $validTimeInSecond = false
    ): string {
        // check the time validation is available
        // make the time validation if it is available
        if ($validTimeInSecond != false) {
            $nameOfTheToken .= EndeCorder::SEPARATOR . (time() + $validTimeInSecond);
        }

        // return the encrypted key
        return EndeCorder::SSLEncryption(
            $nameOfTheToken,
            base64_encode($uniqueKey . self::SELF_TOKEN_KEY)
        );
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
        string $token,
        string $nameOfTheToken,
        string $uniqueKey = 'ksoftm'
    ): TokenResult {
        // decrypt the data and check it is a valid token
        $token = EndeCorder::SSLDecryption(
            $token,
            base64_encode($uniqueKey . self::SELF_TOKEN_KEY)
        );

        if (!empty($token)) {
            // separate the name and time
            [$name, $time] = explode(EndeCorder::SEPARATOR, $token, 2) + [null, null];

            if (!empty($name) && $name === $nameOfTheToken) {
                if (!empty($time)) {
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

    /**
     * make a big hash key
     *
     * @param string $data
     * @param boolean $binary
     * @param string $method
     *
     * @return string
     */
    function BigHash(string $data, bool $binary = false, string $method = 'sha512'): string
    {
        return hash($method, $data, $binary);
    }

    /**
     * create a hash password.
     *
     * @param string $password
     * @param integer $cost
     *
     * @return string
     */
    function HashedPassword(string $password, int $cost = 8): string
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
    function VerifyHashedPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
