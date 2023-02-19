<?php

namespace Samples\UserService\Repositories;

use Ramsey\Uuid\Uuid;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserRepository
{
    private EventDispatcherInterface $eventDispatcher;

    /**
     * UserRepository constructor.
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Exception
     */
    public function save(User $user)
    {
        // save user then dispatch event
        $this->eventDispatcher->dispatch(
            new UserEvent(UserEvent::USER_CREATED, UserEvent::VERSION, Uuid::uuid4()->toString(), $user->toArray()),
            UserEvent::USER_CREATED
        );
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Exception
     */
    public function update(User $user)
    {
        // update user then dispatch event
        $this->eventDispatcher->dispatch(
            new UserEvent(UserEvent::USER_UPDATED, UserEvent::VERSION, Uuid::uuid4()->toString(), $user->toArray()),
            UserEvent::USER_UPDATED
        );
    }
}
