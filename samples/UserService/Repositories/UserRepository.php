<?php

namespace Samples\UserService\Repositories;

use Samples\UserService\Entities\User;

class UserRepository
{
    public function save(User $user)
    {
        return true;
    }

    public function update(User $user)
    {
        return true;
    }
}
