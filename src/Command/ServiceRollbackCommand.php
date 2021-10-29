<?php

namespace Micronative\EventSchema\Command;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Event\EventValidator;
use Micronative\EventSchema\Service\RollbackInterface;

class ServiceRollbackCommand implements CommandInterface
{
    protected EventValidator $validator;
    protected RollbackInterface $service;
    protected AbstractEvent $event;

    /**
     * RollbackCommand constructor.
     * @param \Micronative\EventSchema\Event\EventValidator $validator
     * @param \Micronative\EventSchema\Service\RollbackInterface $service
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     */
    public function __construct(EventValidator $validator, RollbackInterface $service, AbstractEvent $event)
    {
        $this->validator = $validator;
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
        $this->validator->validateEvent($this->event, false);

        return $this->service->rollback($this->event);
    }
}
