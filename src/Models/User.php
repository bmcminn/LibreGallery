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

    const TABLE_NAME = 'user';

    const $model;

    public static array $roles = [
        'admin',
        'moderator',
        'super',
        'user',
    ];


    public static function create($data = []) {
        $user = R::dispense(self::TABLE_NAME/**/);

        $user->dateofbirth  = $data['dateofbirth'] ?? null;
        $user->email        = $data['email'];
        $user->email_base   = Validator::stripEmailSubaddress($data['email']);
        // $user->firstname    = $data[''];
        // $user->lastname     = $data[''];
        // $user->name         = $data['TEST_USER_FIRST'] . ' ' . $_ENV['TEST_USER_LAST'];
        $user->password     = Hash::password($data['password']);
        $user->roles        = '';
        $user->uuid         = uuid4();

        $user->createdAt    = R::isoDateTime(); // now();
        $user->updatedAt    = null;
        $user->deletedAt    = null;

        R::store($user);

        // TODO: Log event
        // TODO: create event entry
        Event::log('user created', $user->uuid);

        return $user;
    }


    public static function findByUuid(string $uuid) {
        return R::findOne(self::TABLE_NAME, 'uuid = :uuid', [ 'uuid' => $uuid ]);
    }


    public static function findByEmail(string $email) {
        $email = trim($email);
        return R::findOne(self::TABLE_NAME, 'email = :email', [ 'email' => $email ]);
    }


    public static function update($data = []) {

    }

    public static function delete($userId) {

    }


    public static function addRole($role) {

    }


    public static function removeRole($role) {

    }

}
