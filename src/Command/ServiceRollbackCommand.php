<?php

namespace Micronative\EventSchema\Command;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\RollbackInterface;
use Micronative\EventSchema\Validators\EventValidator;

class ServiceRollbackCommand implements CommandInterface
{
    /** @var \Micronative\EventSchema\Validators\EventValidator */
    protected $validator;

    /** @var \Micronative\EventSchema\Service\RollbackInterface */
    protected $service;

    /** @var \Micronative\EventSchema\Event\AbstractEvent */
    protected $event;

    /**
     * RollbackCommand constructor.
     * @param \Micronative\EventSchema\Validators\EventValidator $validator
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