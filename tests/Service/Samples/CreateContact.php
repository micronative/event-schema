<?php

namespace Tests\Service\Samples;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\AbstractService;
use Micronative\EventSchema\Service\RollbackInterface;
use Micronative\EventSchema\Service\ServiceInterface;

class CreateContact extends AbstractService implements RollbackInterface
{
    public function consume(AbstractEvent $event = null)
    {
        return new SampleEvent("SomeName");
    }

    public function rollback(AbstractEvent $event = null)
    {
       return 'Contact creation has been rollback.';
    }
}
