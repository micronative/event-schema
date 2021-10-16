<?php

namespace Samples\UserService\Repositories;

use Samples\UserService\Entities\User;

class UserRepository
{
    const USER_CREATED = 'User.Created';
    const USER_UPDATED = 'User.Updated';

    public function save(User $user)
    {
        return true;
    }

    public function update(User $user)
    {
        return true;
    }
}
