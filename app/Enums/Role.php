<?php

namespace App\Enums;

enum Role: string
{
    case Reader = 'reader';
    case Author = 'author';
    case Admin = 'admin';
}
