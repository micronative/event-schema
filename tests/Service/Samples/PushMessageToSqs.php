<?php

namespace Tests\Service\Samples;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\AbstractService;
use Micronative\EventSchema\Service\ServiceInterface;

class PushMessageToSqs extends AbstractService implements ServiceInterface
{
    public function consume(AbstractEvent $event = null)
    {
        return true;
    }
}
