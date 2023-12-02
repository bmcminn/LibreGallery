<?php

namespace App\Helpers;

use ErrorException;


/**
 * A simple token class for generating random hash tokens.
 *
 * This utility is not gauranateed to be cryptographically secure and is only
 *  intended to be used for generating token identifiers
 */
class Token {

    const TYPE_PASSWORD_RESET   = 'passwordreset';
    const TYPE_JWT              = 'jwt';
    const TYPE_USER_AUTH        = 'userauth';


    private static array $algos = [];


    /**
     * { function_description }

     * Running list of algos from hash_hmac_algos()
     *  "md2"
     *  "md4"
     *  "md5"
     *  "sha1"
     *  "sha224"
     *  "sha256"
     *  "sha384"
     *  "sha512/224"
     *  "sha512/256"
     *  "sha512"
     *  "sha3-224"
     *  "sha3-256"
     *  "sha3-384"
     *  "sha3-512"
     *  "ripemd128"
     *  "ripemd160"
     *  "ripemd256"
     *  "ripemd320"
     *  "whirlpool"
     *  "tiger128,3"
     *  "tiger160,3"
     *  "tiger192,3"
     *  "tiger128,4"
     *  "tiger160,4"
     *  "tiger192,4"
     *  "snefru"
     *  "snefru256"
     *  "gost"
     *  "gost-crypto"
     *  "haval128,3"
     *  "haval160,3"
     *  "haval192,3"
     *  "haval224,3"
     *  "haval256,3"
     *  "haval128,4"
     *  "haval160,4"
     *  "haval192,4"
     *  "haval224,4"
     *  "haval256,4"
     *  "haval128,5"
     *  "haval160,5"
     *  "haval192,5"
     *  "haval224,5"
     *  "haval256,5"
     */
    public static function setup() : void {
        self::$algos = hash_hmac_algos();
    }


    /**
     * { function_description }
     *
     * @param       string      $type       The type
     * @param       <array>     $options    The options
     * @property        any         $data       Data to be hashed
     * @property        any         $secret     Shared secret key used to genreate the HMAC variant
     * @property        bool        $binary     True to output raw binary data; default false for lowercase hexits
     *
     * @throws     \ErrorException  (description)
     *
     * @return     string                   The resulting HMAC hash string
     */
    public static function generate(string $type, array $options = []) : string {

        if (empty(self::$algos)) {
            throw new \ErrorException('Token::setup() must be run before calling any generate() method.');
        }

        $options = array_replace_recursive([
            'data'      => time() . rand(1000, 10000),
            'secret'    => 'itsasecrettoeveryone',
            'binary'    => false,
        ]);

        $type       = trim(strtolower($type));
        $data       = $options['data'];
        $hashkey    = $options['secret'];
        $binary     = (bool) $options['binary'];

        if (!in_array($type, self::$algos)) {
            throw new \ErrorException("HMAC Algorithm {$type} does not exist. Use a valid hash type defined in https://www.php.net/manual/en/function.hash-hmac-algos.php");
            exit;
        }

        return hash_hmac($type, $data, $hashkey, $binary);
    }



    public static function generateMD5(array $options = []) {
        return self::generate('md5', $options);
    }


    public static function generateSHA256(array $options = []) {
        return self::generate('sha256', $options);
    }


    public static function generateSHA512(array $options = []) {
        return self::generate('sha512', $options);
    }


    public static function generateWhirlpool(array $options = []) {
        return self::generate('whirlpool', $options);
    }


    public static function generateGost(array $options = []) {
        return self::generate('gost', $options);
    }


    public static function generateHaval(array $options = []) {
        return self::generate('haval256,3', $options);
    }

}
