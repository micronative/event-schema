<?php

namespace Samples\UserService\Events;

use Micronative\EventSchema\ValidatorInterface;
use Micronative\MockBroker\PublisherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber implements EventSubscriberInterface
{

    private ValidatorInterface $validator;
    private PublisherInterface $publisher;

    public function __construct(ValidatorInterface $validator, PublisherInterface $publisher)
    {
        $this->validator = $validator;
        $this->publisher = $publisher;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvent::USER_CREATED => 'onUserCreated',
            UserEvent::USER_UPDATED => 'onUserUpdated',
        ];
    }

    public function onUserCreated(UserEvent $userEvent)
    {
        if ($this->validator->validate($userEvent, true)) {
            echo "-- Start publishing event to broker: {$userEvent->getName()}" . PHP_EOL;
            $this->publisher->publish($userEvent->toJson(), UserEvent::USER_EVENT_TOPIC);
            echo "-- Finish publishing event to broker: {$userEvent->getName()}" . PHP_EOL;
        }
    }

    public function onUserUpdated(UserEvent $userEvent)
    {
        if ($this->validator->validate($userEvent, true)) {
            echo "-- Start publishing event to broker: {$userEvent->getName()}" . PHP_EOL;
            $this->publisher->publish($userEvent->toJson(), UserEvent::USER_EVENT_TOPIC);
            echo "-- Finish publishing event to broker: {$userEvent->getName()}" . PHP_EOL;
        }
    }
}
