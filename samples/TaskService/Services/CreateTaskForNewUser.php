<?php

namespace Samples\TaskService\Services;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\AbstractService;
use Micronative\EventSchema\Service\RollbackInterface;

class CreateTaskForNewUser extends AbstractService implements RollbackInterface
{
    public function consume(AbstractEvent $event = null)
    {
        echo "Task has been created for new user: {$event->getPayload()['name']}, {$event->getPayload()['email']}" . PHP_EOL;

        return $event;
    }

    public function rollback(AbstractEvent $event = null)
    {
        echo 'Task has been rollback.' . PHP_EOL;

        return $event;
    }
}
