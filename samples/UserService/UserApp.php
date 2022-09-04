<?php

namespace Samples\UserService;

use Micronative\EventSchema\Producer;
use Samples\MessageBroker\MockBroker;
use Samples\UserService\Broadcast\MockPublisher;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEventSubscriber;
use Samples\UserService\Repositories\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserApp
{
    private UserRepository $userRepository;

    /**
     * UserApp constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(MockBroker $broker = null)
    {
        $eventSubscriber = new UserEventSubscriber(
            new Producer(dirname(__FILE__), ["/assets/configs/events.yml"]),
            new MockPublisher($broker)
        );
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($eventSubscriber);
        $this->userRepository = new UserRepository($eventDispatcher);
    }

    /**
     * @param string $name
     * @param string $email
     */
    public function createUser(string $name, string $email)
    {
        $this->userRepository->save(new User($name, $email));
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     */
    public function updateUser(User $user)
    {
        $this->userRepository->update($user);
    }
}
