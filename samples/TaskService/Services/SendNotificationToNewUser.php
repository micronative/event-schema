<?php

namespace Samples\TaskService\Services;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\ServiceInterface;

class SendNotificationToNewUser implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Notification has been sent to new user: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;

        return $event;
    }
}
