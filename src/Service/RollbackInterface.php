<?php

namespace Micronative\EventSchema\Service;

use Micronative\EventSchema\Event\AbstractEvent;

interface RollbackInterface extends ServiceInterface
{

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent|null $event
     * @return \Micronative\EventSchema\Event\AbstractEvent|bool
     */
    public function rollback(AbstractEvent $event = null);

}
