<?php

namespace Micronative\EventSchema;

use Micronative\EventSchema\Event\AbstractEvent;

interface ConsumerInterface
{
    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param array|null $filteredEvents
     * @return bool
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ConsumerException
     */
    public function process(AbstractEvent $event, array $filteredEvents = null);

    /**
     * @param string|\Micronative\EventSchema\Event\AbstractEvent $event
     * @param array|null $filteredEvents
     * @return bool
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConsumerException
     */
    public function rollback(AbstractEvent $event, array $filteredEvents = null);
}
