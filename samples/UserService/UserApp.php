<?php

namespace Samples\UserService;

use Micronative\EventSchema\Validator;
use Micronative\MockBroker\Broker;
use Micronative\MockBroker\Publisher as MockPublisher;
use Samples\UserService\Messaging\Publisher;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEventSubscriber;
use Samples\UserService\Repositories\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;

class UserApp
{
    private UserRepository $userRepository;

    /**
     * UserApp constructor.
     * @param \Micronative\MockBroker\Broker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(Broker $broker = null)
    {
        $eventSubscriber = new UserEventSubscriber(
            new Publisher(
                new MockPublisher($broker),
                new Validator(dirname(__FILE__), ["/assets/configs/out_events.yml"]))
        );
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addSubscriber($eventSubscriber);
        $this->userRepository = new UserRepository($eventDispatcher);
    }

    /**
     * @param string $name
     * @param string|null $email
     * @throws \Exception
     */
    public function createUser(string $name, ?string $email = null)
    {
        $this->userRepository->save(new User($name, $email));
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Exception
     */
    public function updateUser(User $user, string $name, string $email)
    {
        $user->setName($name)->setEmail($email);
        $this->userRepository->update($user);
    }
}
