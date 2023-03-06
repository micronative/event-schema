<?php

namespace Samples\UserService\Messaging;
use Micronative\EventSchema\ValidatorInterface;
use Samples\UserService\Events\UserEvent;
use Micronative\MockBroker\PublisherInterface as MockPublisherInterface;

class Publisher implements PublisherInterface
{
    private ValidatorInterface $validator;
    private MockPublisherInterface $publisher;

    /**
     * @param MockPublisherInterface $publisher
     * @param ValidatorInterface|null $validator
     */
    public function __construct(MockPublisherInterface $publisher, ValidatorInterface $validator = null)
    {
        $this->validator = $validator;
        $this->publisher = $publisher;
    }

    public function publishEvent(UserEvent $userEvent)
    {
        echo "-- Validating outgoing event message: {$userEvent->getName()}" . PHP_EOL;
        if ($this->validator->validate($userEvent, true)) {
            echo "-- Start publishing event message to broker: {$userEvent->getName()}" . PHP_EOL;
            $this->publisher->publish($userEvent->toJson(), UserEvent::USER_EVENT_TOPIC);
            echo "-- Finish publishing event message to broker: {$userEvent->getName()}" . PHP_EOL;
        }
    }
}