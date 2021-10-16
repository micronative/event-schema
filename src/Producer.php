<?php

namespace Micronative\EventSchema;

use JsonSchema\Validator;
use Micronative\EventSchema\Command\EventValidateCommand;
use Micronative\EventSchema\Config\Consumer\EventConfigRegister;
use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Validators\EventValidator;

class Producer implements ProducerInterface
{
    /** @var \Micronative\EventSchema\Config\Producer\EventConfigRegister */
    protected $eventConfigRegister;

    /** @var \Micronative\EventSchema\Validators\EventValidator */
    protected $eventValidator;

    /** @var string|null */
    protected $schemaDir;

    /**
     * Producer constructor.
     *
     * @param array|null $eventConfigs
     * @param string|null $schemaDir a relative dir from where the schemas are stored
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(array $eventConfigs = null, string $schemaDir = null)
    {
        $this->schemaDir = $schemaDir;
        $this->eventConfigRegister = new EventConfigRegister($eventConfigs);
        $this->eventValidator = new EventValidator($this->schemaDir, new Validator());
        $this->loadConfigs();
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param bool $applyDefaultValues
     * @return bool|\Micronative\EventSchema\Event\AbstractEvent|void
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function validate(AbstractEvent $event, bool $applyDefaultValues = false)
    {
        /**
         * If event has no schema then try with schema from config
         */
        if (empty($event->getSchemaFile())) {
            /** @var \Micronative\EventSchema\Config\Producer\EventConfig $eventConfig */
            $eventConfig = $this->eventConfigRegister->retrieveEventConfig($event->getName(), $event->getVersion());
            $event->setSchemaFile($eventConfig->getSchemaFile());
        }
        $validateCommand = new EventValidateCommand($this->eventValidator, $event, $applyDefaultValues);

        return $validateCommand->execute();
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    private function loadConfigs()
    {
        $this->eventConfigRegister->loadEventConfigs();
    }

    /**
     * @return string|null
     */
    public function getSchemaDir(): ?string
    {
        return $this->schemaDir;
    }

    /**
     * @param string|null $schemaDir
     * @return \Micronative\EventSchema\Producer
     */
    public function setSchemaDir(?string $schemaDir): Producer
    {
        $this->schemaDir = $schemaDir;

        return $this;
    }
}