<?php

namespace App\Models;

use App\Enums\TokenType;

use RedBeanPHP\Facade as R;
use RedBeanPHP\OODBBean as RBean;


class TokenModel {

    // const PASSWORD_RESET   = 'passwordreset';
    // const TYPE_JWT              = 'jwt';
    // const TYPE_USER_AUTH        = 'userauth';

    const TABLE_NAME = 'token';

    private string $user_uuid;
    private string $token;
    private int $timeToLive;

    private RBean $model;


    public function __construct(string $user_uuid) {
        $this->user_uuid = $user_uuid;

        return $this;
    }


    public function setType(TokenType $tokenType) {
        $this->type = $tokenType;

        return $this;
    }


    public function setMaxAttempts(int $maxAttempts) {
        $this->maxAttempts = clamp(1, 10, $maxAttempts);

        return $this;
    }


    public function setExpiration(int $timeToLive) {
        $this->timeToLive = now() + $timeToLive;

        return $this;
    }


    public function setToken(string $token) {
        $this->token = $token;

        return $this;
    }



    public function toString() {
        return $this->token->token;
    }



    public function findTokensByUser(string $user_uuid, string $type) {
        return R::find(self::TABLE_NAME, 'user_uuid = :user_uuid AND type = :type AND deleted_at IS NULL',
            [
                'user_uuid' => $this->user_uuid,
                'type'      => $this->type,
                'now'       => time(),
            ]);
    }


    public static function findToken(string $token, string $type) {
        return R::findOne(self::TABLE_NAME, 'token = :token AND type = :type AND expires_at > :now AND deleted_at IS NULL',
            [
                'token' => $token,
                'now'   => time(),
                'type'  => $type,
            ]);
    }


    /**
     * Creates a token and persists to database
     *
     * @param      string  $type     The type
     * @param      int     $expires  The expires time to live in minutes
     */
    public function createToken() {
        $bean = R::dispense(self::TableName);

        $bean->type         = $this->type;
        $bean->token        = $this->token;
        $bean->userUuid     = $this->user_uuid;
        $bean->expiresAt    = $this->expiration;
        $bean->maxAttempts  = $this->max_attempts;

        R::store($bean);

        $this->model = $bean;

        return $bean->token;
    }


    public function createPasswordResetToken() {
        return $this->
            ->setType(TokenType::PASSWORD_RESET)
            ->setMaxAttempts(3)
            ->setExpiration(minutes(10))
            ->createToken(Token::generateSHA256([
                'secret' => 'PasswordResetTokenSecretPhraseHere',
            ]))
            ;
    }


    function deleteToken(RedBeanPHP\OODBBean $bean) :bool {
        return R::trash($bean);
        // $bean = getToken($token, $type);
        // return R::trash($bean);
    }


    // function checkToken(string $token, string $type) :bool {
    //     return !!getToken($token, $type);
    // }


    function attemptToken(string $token, string $type) :bool {
        $bean = getToken($token, $type);

        if ((int) $bean->attempts >= (int) $bean->maxAttempts) {
            $bean->deletedAt = now();
            R::store($bean);
            return false;
        }

        return true;
    }



    function clearOldPasswordResetTokens(string $user_uuid) {
        $tokens = self::findTokensByUser($user_uuid, TokenType::PASSWORD_RESET);

        if (empty(count($tokens))) { return; }

        R::begin();
        try{
            foreach ($tokens as $token) {
                // $token->expiresAt = now();
                R::trash($token);
                // $token->deletedAt = now();
                // R::store($token);
            }
            R::commit();
        } catch(\Exception $e) {
            R::rollback();
        }
    }
