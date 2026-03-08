<?php

namespace App\Models;

use App\Models\Administration\User\User as AdministrationUser;
use Database\Factories\Administration\User\UserFactory;

class User extends AdministrationUser
{
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
