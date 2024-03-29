<?php

namespace Micronative\EventSchema;

use JsonSchema\Validator as JsonValidator;
use Micronative\EventSchema\Command\EventValidateCommand;
use Micronative\EventSchema\Config\Consumer\EventConfigRegister;
use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Event\EventValidator;

class Validator implements ValidatorInterface
{
    protected EventConfigRegister $eventConfigRegister;
    protected EventValidator $eventValidator;
    protected ?string $assetDir;

    /**
     * Producer constructor.
     *
     * @param array|null $eventConfigs
     * @param string|null $assetDir a relative dir from where the assets are stored
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(?string $assetDir = null, ?array $eventConfigs = null)
    {
        $this->assetDir = $assetDir;
        $this->eventConfigRegister = new EventConfigRegister($this->assetDir, $eventConfigs);
        $this->eventValidator = new EventValidator($this->assetDir, new JsonValidator());
        $this->loadConfigs();
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param bool $applyDefaultValues
     * @return bool|\Micronative\EventSchema\Event\AbstractEvent|void
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function validate(AbstractEvent $event, ?bool $applyDefaultValues)
    {
        /**
         * If event has no schema then try with schema from config
         */
        if (empty($event->getSchemaFile())) {
            /** @var \Micronative\EventSchema\Config\Producer\EventConfig $eventConfig */
            if (!empty(
            $eventConfig = $this->eventConfigRegister->retrieveEventConfig(
                $event->getName(),
                $event->getVersion()
            )
            )) {
                $event->setSchemaFile($eventConfig->getSchemaFile());
            }
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
    public function getAssetDir(): ?string
    {
        return $this->assetDir;
    }

    /**
     * @param string|null $assetDir
     * @return \Micronative\EventSchema\Validator
     */
    public function setAssetDir(?string $assetDir): Validator
    {
        $this->assetDir = $assetDir;

        return $this;
    }
}
