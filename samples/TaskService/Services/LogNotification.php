<?php

namespace Samples\TaskService\Services;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\AbstractService;

class LogNotification extends AbstractService
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Notification has been logged for: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;
    }
}
