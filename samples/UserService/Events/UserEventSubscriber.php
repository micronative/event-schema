<?php

namespace Samples\UserService\Events;

use Samples\UserService\Messaging\Publisher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserEventSubscriber implements EventSubscriberInterface
{
    private Publisher $publisher;

    public function __construct(Publisher $publisher)
    {
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
        $this->publisher->publishEvent($userEvent);
    }

    public function onUserUpdated(UserEvent $userEvent)
    {
        $this->publisher->publishEvent($userEvent);
    }
}
