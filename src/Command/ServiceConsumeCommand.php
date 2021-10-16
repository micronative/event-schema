<?php

namespace Micronative\EventSchema\Command;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\ServiceInterface;
use Micronative\EventSchema\Validators\EventValidator;

class ServiceConsumeCommand implements CommandInterface
{
    /** @var \Micronative\EventSchema\Validators\EventValidator */
    protected $eventValidator;

    /** @var \Micronative\EventSchema\Service\ServiceInterface */
    protected $service;

    /** @var \Micronative\EventSchema\Event\AbstractEvent */
    protected $event;

    /**
     * ConsumeCommand constructor.
     * @param \Micronative\EventSchema\Validators\EventValidator $validator
     * @param \Micronative\EventSchema\Service\ServiceInterface $service
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     */
    public function __construct(EventValidator $validator, ServiceInterface $service, AbstractEvent $event)
    {
        $this->eventValidator = $validator;
        $this->service = $service;
        $this->event = $event;
    }

    /**
     * @return bool|\Micronative\EventSchema\Event\AbstractEvent
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function execute()
    {
        $this->eventValidator->validateEvent($this->event, false);

        return $this->service->consume($this->event);
    }
}
