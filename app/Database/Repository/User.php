<?php

namespace Database\Repository;

use Ouzo\Utilities\Arrays;
use Database\Interface\Repository;
use Ouzo\Utilities\Strings;

class User extends Repository
{
    public function __construct()
    {
        parent::__construct("tbl_user", \Database\Object\User::class);
    }

    public function getByUsername($username)
    {
        $items = parent::get(order: false);
        $items = Arrays::filter($items, fn ($i) => Strings::equal($i->username, $username));

        return array_values($items);
    }
}
