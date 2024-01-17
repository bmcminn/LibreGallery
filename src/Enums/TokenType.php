<?php

namespace App\Enums;

enum TokenType {
    public const PASSWORD_RESET   = 'passwordreset';
    public const JWT              = 'jwt';
    public const USER_AUTH        = 'userauth';
}
