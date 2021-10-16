<?php

namespace Samples\TaskService\Services;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\AbstractService;

class LogTask extends AbstractService
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Task has been logged for: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;

        return true;
    }
}
