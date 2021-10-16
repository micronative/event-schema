<?php

namespace Tests\Service\Samples;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\AbstractService;
use Micronative\EventSchema\Service\ServiceInterface;

class CreateTask extends AbstractService
{
    public function consume(AbstractEvent $event = null)
    {
        return 'Task created.';
    }
}
