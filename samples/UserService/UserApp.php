<?php

namespace Samples\UserService;

use Micronative\EventSchema\Producer;
use Ramsey\Uuid\Uuid;
use Samples\MessageBroker\MockBroker;
use Samples\UserService\Broadcast\MockPublisher;
use Samples\UserService\Entities\User;
use Samples\UserService\Events\UserEvent;
use Samples\UserService\Repositories\UserRepository;

class UserApp
{
    private MockPublisher $publisher;
    private UserRepository $userRepository;
    private Producer $producer;

    /**
     * UserApp constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(MockBroker $broker = null)
    {
        $this->publisher = new MockPublisher($broker);
        $this->userRepository = new UserRepository();
        $assetDir = dirname(__FILE__);
        $this->producer = new Producer($assetDir, ["/assets/configs/events.yml"]);
    }

    /**
     * @param string $name
     * @param string $email
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function createUser(string $name, string $email)
    {
        $user = new User($name, $email);
        if ($this->userRepository->save($user)) {
            $userEvent = new UserEvent(UserRepository::USER_CREATED, null, Uuid::uuid4()->toString(), $user->toArray());

            if ($this->producer->validate($userEvent, true)) {
                echo "-- Start publishing event to broker: {$userEvent->getName()}".PHP_EOL;
                $this->publisher->publish($userEvent->jsonSerialize());
                echo "-- Finish publishing event to broker: {$userEvent->getName()}".PHP_EOL;
            }
        }
    }

    /**
     * @param \Samples\UserService\Entities\User $user
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function updateUser(User $user)
    {
        if ($this->userRepository->update($user)) {
            $userEvent = new UserEvent(UserRepository::USER_UPDATED, null, Uuid::uuid4()->toString(), $user->toArray());

            if ($this->producer->validate($userEvent, true)) {
                echo "-- Start publishing event to broker: {$userEvent->getName()}".PHP_EOL;
                $this->publisher->publish($userEvent->jsonSerialize());
                echo "-- Finish publishing event to broker: {$userEvent->getName()}".PHP_EOL;
            }
        }
    }
}
