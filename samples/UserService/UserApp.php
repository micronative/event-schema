<?php

namespace Samples\UserService;

use Micronative\EventSchema\Validator;
use Samples\MessageBroker\Broker;
use Samples\MessageBroker\Publisher;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEventSubscriber;
use Samples\UserService\Repositories\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserApp
{
    private UserRepository $userRepository;

    /**
     * UserApp constructor.
     * @param \Samples\MessageBroker\Broker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(Broker $broker = null)
    {
        $eventSubscriber = new UserEventSubscriber(
            new Validator(dirname(__FILE__), ["/assets/configs/events.yml"]),
            new Publisher($broker)
        );
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($eventSubscriber);
        $this->userRepository = new UserRepository($eventDispatcher);
    }

    /**
     * @param string $name
     * @param string $email
     * @throws \Exception
     */
    public function createUser(string $name, string $email)
    {
        $this->userRepository->save(new User($name, $email));
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Exception
     */
    public function updateUser(User $user)
    {
        $this->userRepository->update($user);
    }
}
