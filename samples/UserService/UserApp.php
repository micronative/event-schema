<?php

namespace Samples\UserService;

use Micronative\EventSchema\Producer;
use Ramsey\Uuid\Uuid;
use Samples\MessageBroker\MockBroker;
use Samples\UserService\Broadcast\MockPublisher;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEvent;
use Samples\UserService\Events\UserEventSubscriber;
use Samples\UserService\Repositories\UserRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserApp
{
    private UserRepository $userRepository;
    private EventDispatcherInterface $eventDispatcher;
    private EventSubscriberInterface $eventSubscriber;

    /**
     * UserApp constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(MockBroker $broker = null)
    {
        $this->userRepository = new UserRepository();
        $this->eventSubscriber = new UserEventSubscriber(
            new Producer(dirname(__FILE__), ["/assets/configs/events.yml"]),
            new MockPublisher($broker)
        );
        $this->eventDispatcher = new EventDispatcher();
        $this->eventDispatcher->addSubscriber($this->eventSubscriber);
    }

    /**
     * @param string $name
     * @param string $email
     * @throws \Exception
     */
    public function createUser(string $name, string $email)
    {
        $user = new User($name, $email);
        if ($this->userRepository->save($user)) {
            $userEvent = new UserEvent(UserEvent::USER_CREATED, null, Uuid::uuid4()->toString(), $user->toArray());
            $this->eventDispatcher->dispatch($userEvent, UserEvent::USER_CREATED);
        }
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Exception
     */
    public function updateUser(User $user)
    {
        if ($this->userRepository->update($user)) {
            $userEvent = new UserEvent(UserEvent::USER_UPDATED, null, Uuid::uuid4()->toString(), $user->toArray());
            $this->eventDispatcher->dispatch($userEvent, UserEvent::USER_UPDATED);
        }
    }
}
