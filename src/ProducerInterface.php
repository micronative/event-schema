<?php

namespace Micronative\EventSchema;

use Micronative\EventSchema\Event\AbstractEvent;

interface ProducerInterface
{
    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param bool $applyDefaultValues
     * @return true
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function validate(AbstractEvent $event, bool $applyDefaultValues = false);
}
