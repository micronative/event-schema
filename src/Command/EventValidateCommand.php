<?php

namespace Micronative\EventSchema\Command;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Event\EventValidator;

class EventValidateCommand implements CommandInterface
{
    private EventValidator $eventValidator;
    private AbstractEvent $event;
    private bool $applyDefaultValues;

    /**
     * EventValidateCommand constructor.
     * @param \Micronative\EventSchema\Event\EventValidator $validator
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param bool $applyDefaultValues
     */
    public function __construct(
        EventValidator $validator,
        AbstractEvent $event,
        bool $applyDefaultValues
    ) {
        $this->eventValidator = $validator;
        $this->event = $event;
        $this->applyDefaultValues = $applyDefaultValues;
    }

    /**
     * @return true
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function execute()
    {
        return $this->eventValidator->validateEvent($this->event, $this->applyDefaultValues);
    }
}
