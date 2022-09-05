<?php

namespace Micronative\EventSchema;

use Micronative\EventSchema\Event\AbstractEvent;

interface ValidatorInterface
{
    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param bool $applyDefaultValues
     * @return true
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function validate(AbstractEvent $event, bool $applyDefaultValues = false);
}
