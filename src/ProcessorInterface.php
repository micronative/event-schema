<?php

namespace Micronative\EventSchema;

use Micronative\EventSchema\Event\AbstractEvent;

interface ProcessorInterface
{
    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param array|null $filteredEvents
     * @return bool
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     */
    public function process(AbstractEvent $event, array $filteredEvents = null);

    /**
     * @param string|\Micronative\EventSchema\Event\AbstractEvent $event
     * @param array|null $filteredEvents
     * @return bool
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     */
    public function rollback(AbstractEvent $event, array $filteredEvents = null);
}
