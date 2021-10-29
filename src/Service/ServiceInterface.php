<?php

namespace Micronative\EventSchema\Service;

use Micronative\EventSchema\Event\AbstractEvent;

interface ServiceInterface
{
    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @return \Micronative\EventSchema\Event\AbstractEvent|bool
     */
    public function consume(AbstractEvent $event);
}
