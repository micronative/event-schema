<?php

namespace Micronative\EventSchema\Validators;

use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Service\ServiceInterface;

class ServiceValidator extends EventValidator
{
    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param \Micronative\EventSchema\Service\ServiceInterface $service
     * @param bool $applyPayloadDefaultValues
     * @return true
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function validateService(AbstractEvent $event, ServiceInterface $service, bool $applyPayloadDefaultValues = false)
    {
        if (empty($schema = $service->getSchema())) {
            return true;
        }
        $event->setSchemaFile($schema);

        return $this->validateEvent($event, $applyPayloadDefaultValues);
    }
}
