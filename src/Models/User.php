<?php

namespace App\Models;

use App\Helpers\Event;
use App\Helpers\Validator;
use App\Models\Session;
use App\Helpers\Hash;


use RedBeanPHP\Facade as R;

// TODO setup database sessions table; store createdAt, expiresAt, id

enum UserRoles {
    case Admin      => 'admin';
    case Moderator  => 'moderator';
    case Super      => 'super';
    case User       => 'user';
}



class User {

    const TABLE_NAME        = 'user';

    const $model;

    const DEFAULT_ROLES     = '';

    const ROLE_ADMIN        = 'admin';
    const ROLE_MODERATOR    = 'moderator';
    const ROLE_SUPER        = 'super';
    const ROLE_USER         = 'user';


    public static array $roles = [
        'admin',
        'moderator',
        'super',
        'user',
    ];



    const RESTRICTED_KEYS = [
        'id',
        'uuid',
        'roles',
        'created_at',
        'deleted_at',
        'last_login',
    ];


    private $user = null;


    public function __construct() {

    }


    public static function create(array $data = []) {

        $data = array_replace([
            'roles'     => self::DEFAULT_ROLES,
            'firstname' => null,
            'lastname'  => null,
        ], $data);

        $user = R::dispense(self::TABLE_NAME);

        $user->dateofbirth  = $data['dateofbirth'] ?? null;
        $user->email        = $data['email'];
        $user->email_base   = Validator::stripEmailSubaddress($data['email']);
        $user->firstname    = $data['firstname'];
        $user->lastname     = $data['lastname'];
        $user->password     = Hash::password($data['password']);
        $user->roles        = $data['roles'];
        $user->uuid         = uuid4();

        $user->createdAt    = R::isoDateTime(); // now();
        $user->updatedAt    = null;
        $user->deletedAt    = null;
        $user->lastLogin    = null;

        R::store($user);

        // TODO: Log event
        // TODO: create event entry
        Event::log('user created', $user->uuid);

        return $user;
    }


    /**
     * Finds a by uuid.
     *
     * @param      string  $uuid   The uuid
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function findByUuid(string $uuid) {
        return R::findOne(self::TABLE_NAME, 'uuid = :uuid', [ 'uuid' => $uuid ]);
    }



    /**
     * Finds a by email or username.
     *
     * @param      string  $value  The value
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function findByEmail(string $email) {
        $email = trim($email);
        return R::findOne(self::TABLE_NAME, 'email = :email', [ 'email' => $email ]);
    }

    // public static function findByEmailOrUsername(string $value) {
    //     $value = trim($value);
    //     return R::findOne(self::TABLE_NAME, 'email = :emailusername OR username = :emailusername', [ 'emailusername' => $email ]);
    // }


    /**
     * Updates the given bean.
     *
     * @param      array   $bean   The bean
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public static function update(array $data = []) {

        $data = restrictKeys($data, self::RESTRICTED_KEYS);

        foreach ($data as $key => $value) {
            $this->user[$key] = $value;
        }

        return $this->user->store();
    }


    /**
     * Deletes the given user identifier.
     *
     * @param      <type>  $userId  The user identifier
     */
    public static function delete($userId) {

    }


    /**
     * Adds a role.
     *
     * @param      <type>  $role   The role
     */
    public static function addRole($role) {



    }


    /**
     * Removes a role.
     *
     * @param      <type>  $role   The role
     */
    public static function removeRole($role) {

    }

}
