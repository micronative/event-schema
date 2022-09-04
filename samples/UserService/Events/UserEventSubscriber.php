<?php

namespace Samples\UserService\Events;

use Micronative\EventSchema\ProducerInterface;
use Samples\UserService\Broadcast\PublisherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber implements EventSubscriberInterface
{
    private ProducerInterface $producer;
    private PublisherInterface $publisher;

    public function __construct(ProducerInterface $producer, PublisherInterface $publisher)
    {
        $this->producer = $producer;
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
        if ($this->producer->validate($userEvent, true)) {
            echo "-- Start publishing event to broker: {$userEvent->getName()}" . PHP_EOL;
            $this->publisher->publish($userEvent->toJson());
            echo "-- Finish publishing event to broker: {$userEvent->getName()}" . PHP_EOL;
        }
    }

    public function onUserUpdated(UserEvent $userEvent)
    {
        if ($this->producer->validate($userEvent, true)) {
            echo "-- Start publishing event to broker: {$userEvent->getName()}" . PHP_EOL;
            $this->publisher->publish($userEvent->toJson());
            echo "-- Finish publishing event to broker: {$userEvent->getName()}" . PHP_EOL;
        }
    }
}
