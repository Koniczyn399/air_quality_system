<?php

namespace App\Enums\Auth;

use App\Enums\Traits\EnumToArray;

enum RoleType: string
{
    use EnumToArray;

    case ADMIN = 'admin';
    case MAINTEINER = 'mainteiner';
    case USER = 'user';
}
