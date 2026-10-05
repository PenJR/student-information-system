<?php

namespace App\Enums;

enum RoleName: string
{
    case ADMINISTRATOR = 'ADMINISTRATOR';
    case STAFF = 'STAFF';
    case INSTRUCTOR = 'INSTRUCTOR';
    case STUDENT = 'STUDENT';
}