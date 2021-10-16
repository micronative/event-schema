<?php

namespace Micronative\EventSchema\Command;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Validators\EventValidator;

class EventValidateCommand implements CommandInterface
{
    /** @var \Micronative\EventSchema\Validators\EventValidator */
    private $eventValidator;

    /** @var \Micronative\EventSchema\Event\AbstractEvent */
    private $event;

    /** @var bool */
    private $applyDefaultValues;

    /**
     * EventValidateCommand constructor.
     * @param \Micronative\EventSchema\Validators\EventValidator $validator
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param bool $applyDefaultValues
     */
    public function __construct(
        EventValidator $validator,
        AbstractEvent $event,
        bool $applyDefaultValues = false
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
