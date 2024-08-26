<?php

namespace App\Entity;

enum Roles: string
{
    case user = "ROLE_USER";
    case worker = "ROLE_WORKER";
    case admin = "ROLE_ADMIN";
}
