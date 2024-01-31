<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

use ErrorException;


// TODO: move this into the Hash.php class?


/**
 * A simple token class for generating random hash tokens.
 *
 * This utility is not gauranateed to be cryptographically secure and is only
 *  intended to be used for generating token identifiers
 */
class Token {

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
            'data'      => time() + rand(1000, 10000),
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


    public static function generateUuid() {
        return Ramsey\Uuid\Uuid::uuid4()->toString();
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



    public const OTP_ALPHANUMERIC  = 00000001;
    public const OTP_ALPHA         = 00000010;
    public const OTP_NUMERIC       = 00000100;
    public const OTP_MIXED_CASE    = 00001000;
    // define('OTP_ALLOW_PUNCTUATION', 00001000);

    function generateOTP(int $length, int $flags = 0, int $charRange = null) {

        $length     = clamp($length, 2, 256);
        $chars      = "0123456789ABCDEFGHIKLMNOPQRSTUVWXYZ_-";
        $charRange  = $charRange ?? strlen($chars);
        $flags      = $flags ?? self::OTP_ALPHANUMERIC;
        $start      = $flags & self::OTP_ALPHA    ? 10 : 0;
        $end        = $flags & self::OTP_NUMERIC  ? 10 : $charRange;

        $charRange  = clamp($charRange, 2, $end);

        $otp = '';

        for ($i=0; $i < $length; $i++) {
            $ci = rand($start, $end - 1);
            $char = $chars[$ci];

            if ($flags & self::OTP_MIXED_CASE) {
                $char = !!rand(0,1) ? strtolower($char) : $char;
            }

            $otp .= $char;
        }

        return $otp;
    }




    function generateJWT(array $subject=[], array $roles=[], int $nbf=0) {
        $secret = env('JWT_SECRET');

        $token = [];

        $now = time();

        $token['iss'] = env('APP_HOSTNAME');
        $token['exp'] = $now + hours(env('JWT_TTL', 24));
        $token['iat'] = $now;
        $token['nbf'] = $now + $nbf;
        $token['sub'] = $subject;
        $token['scope'] = $roles;

        $encoding = explode('|', env('JWT_ALGORITHM'));

        return JWT::encode($token, $secret, $encoding[0]);
    }


}
