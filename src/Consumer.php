<?php

namespace Micronative\EventSchema;

use JsonSchema\Validator;
use Micronative\EventSchema\Command\ServiceConsumeCommand;
use Micronative\EventSchema\Command\ServiceRollbackCommand;
use Micronative\EventSchema\Config\Consumer\EventConfigRegister;
use Micronative\EventSchema\Config\Consumer\ServiceConfigRegister;
use Micronative\EventSchema\Event\AbstractEvent;
use Micronative\EventSchema\Event\EventValidator;
use Micronative\EventSchema\Exceptions\ConsumerException;
use Micronative\EventSchema\Service\RollbackInterface;
use Micronative\EventSchema\Service\ServiceFactory;
use Micronative\EventSchema\Service\ServiceInterface;
use Psr\Container\ContainerInterface;

class Consumer implements ConsumerInterface
{
    protected EventConfigRegister $eventConfigRegister;
    protected ServiceConfigRegister $serviceConfigRegister;
    protected ServiceFactory $serviceFactory;
    protected EventValidator $eventValidator;
    protected ?ContainerInterface $container;
    protected ?string $assetDir;

    /**
     * ServiceProvider constructor.
     *
     * @param array|null $eventConfigs
     * @param array|null $serviceConfigs
     * @param string|null $assetDir a relative dir from where the assets are stored
     * @param \Psr\Container\ContainerInterface|null $container
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function __construct(
        ?string $assetDir = null,
        ?array $eventConfigs = null,
        ?array $serviceConfigs = null,
        ?ContainerInterface $container = null
    ) {
        $this->assetDir = $assetDir;
        $this->eventConfigRegister = new EventConfigRegister($this->assetDir, $eventConfigs);
        $this->serviceConfigRegister = new ServiceConfigRegister($this->assetDir, $serviceConfigs);
        $this->eventValidator = new EventValidator($this->assetDir, new Validator());
        $this->container = $container;
        $this->serviceFactory = new ServiceFactory();
        $this->loadConfigs();
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent|null $event
     * @param array|null $filteredEvents
     * @param bool $return
     * @return bool|\Micronative\EventSchema\Event\AbstractEvent
     * @throws \Micronative\EventSchema\Exceptions\ConsumerException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function process(?AbstractEvent $event = null, ?array $filteredEvents = null, ?bool $return = false)
    {
        $this->checkFilteredEvents($event, $filteredEvents);
        $serviceClasses = $this->retrieveServiceClasses($event);
        foreach ($serviceClasses as $class) {
            if (empty($serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig($class))) {
                continue;
            }

            if (empty($service = $this->serviceFactory->createService($serviceConfig, $this->container))) {
                continue;
            }

            $callbacks = $serviceConfig->getCallbacks();
            if ($return === true) {
                return $this->runService($event, $service, $callbacks, $return);
            }

            $this->runService($event, $service, $callbacks);
        }

        return true;
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @return bool
     * @throws \Micronative\EventSchema\Exceptions\ConsumerException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function rollback(AbstractEvent $event)
    {
        $serviceClasses = $this->retrieveServiceClasses($event);
        foreach ($serviceClasses as $class) {
            if (empty($serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig($class))) {
                continue;
            }

            if (empty($service = $this->serviceFactory->createService($serviceConfig, $this->container))) {
                continue;
            }

            if ($service instanceof RollbackInterface) {
                $this->rollbackService($event, $service);
            }
        }

        return true;
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    private function loadConfigs()
    {
        $this->eventConfigRegister->loadEventConfigs();
        $this->serviceConfigRegister->loadServiceConfigs();
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param array|null $filteredEvents
     * @throws \Micronative\EventSchema\Exceptions\ConsumerException
     */
    private function checkFilteredEvents(AbstractEvent $event, ?array $filteredEvents = null)
    {
        if(empty($event->getName())){
            throw new ConsumerException(ConsumerException::EMPTY_EVENT_NAME);
        }

        if (!empty($filteredEvents) && !in_array($event->getName(), $filteredEvents)) {
            throw new ConsumerException(ConsumerException::FILTERED_EVENT_ONLY . json_encode($filteredEvents));
        }
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @return string[]
     * @throws \Micronative\EventSchema\Exceptions\ConsumerException
     */
    private function retrieveServiceClasses(AbstractEvent $event)
    {
        /** @var \Micronative\EventSchema\Config\Consumer\EventConfig $eventConfig */
        $eventConfig = $this->eventConfigRegister->retrieveEventConfig($event->getName(), $event->getVersion());
        if (empty($eventConfig)) {
            throw new ConsumerException(
                sprintf(ConsumerException::NO_REGISTER_EVENTS, $event->getName(), $event->getVersion())
            );
        }

        /**
         * Validate the event against the schema in config
         */
        $event->setSchemaFile($eventConfig->getSchemaFile());
        if (empty($serviceClasses = $eventConfig->getServiceClasses())) {
            throw new ConsumerException(
                sprintf(ConsumerException::NO_REGISTER_SERVICES, $event->getName(), $event->getVersion())
            );
        }

        return $serviceClasses;
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param \Micronative\EventSchema\Service\ServiceInterface $service
     * @param array|null $callbacks
     * @param bool $return
     * @return bool|\Micronative\EventSchema\Event\AbstractEvent
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    private function runService(
        AbstractEvent $event,
        ServiceInterface $service,
        array $callbacks = null,
        bool $return = false
    ) {
        $consumeCommand = new ServiceConsumeCommand($this->eventValidator, $service, $event);
        $result = $consumeCommand->execute();
        if ($return === true) {
            return $result;
        }

        if (($result instanceof AbstractEvent) && !empty($callbacks)) {
            return $this->runCallbacks($result, $callbacks);
        }

        return $result;
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param \Micronative\EventSchema\Service\RollbackInterface $service
     * @return bool|\Micronative\EventSchema\Event\AbstractEvent
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    private function rollbackService(AbstractEvent $event, RollbackInterface $service)
    {
        $rollbackCommand = new ServiceRollbackCommand($this->eventValidator, $service, $event);

        return $rollbackCommand->execute();
    }

    /**
     * @param \Micronative\EventSchema\Event\AbstractEvent $event
     * @param array $callbacks
     * @return bool
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    private function runCallbacks(AbstractEvent $event, array $callbacks)
    {
        foreach ($callbacks as $class) {
            if (empty($serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig($class))) {
                continue;
            }

            if (empty($service = $this->serviceFactory->createService($serviceConfig, $this->container))) {
                continue;
            }

            $consumeCommand = new ServiceConsumeCommand($this->eventValidator, $service, $event);
            $consumeCommand->execute();
        }

        return true;
    }

    /**
     * @return \Micronative\EventSchema\Config\Consumer\EventConfigRegister
     */
    public function getEventConfigRegister()
    {
        return $this->eventConfigRegister;
    }

    /**
     * @param \Micronative\EventSchema\Config\Consumer\EventConfigRegister $eventConfigRegister
     * @return \Micronative\EventSchema\Consumer
     */
    public function setEventConfigRegister(EventConfigRegister $eventConfigRegister)
    {
        $this->eventConfigRegister = $eventConfigRegister;

        return $this;
    }

    /**
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfigRegister
     */
    public function getServiceConfigRegister()
    {
        return $this->serviceConfigRegister;
    }

    /**
     * @param \Micronative\EventSchema\Config\Consumer\ServiceConfigRegister $serviceConfigRegister
     * @return \Micronative\EventSchema\Consumer
     */
    public function setServiceConfigRegister(ServiceConfigRegister $serviceConfigRegister)
    {
        $this->serviceConfigRegister = $serviceConfigRegister;

        return $this;
    }

    /**
     * @return \Micronative\EventSchema\Service\ServiceFactory
     */
    public function getServiceFactory(): ServiceFactory
    {
        return $this->serviceFactory;
    }

    /**
     * @param \Micronative\EventSchema\Service\ServiceFactory $serviceFactory
     * @return \Micronative\EventSchema\Consumer
     */
    public function setServiceFactory(ServiceFactory $serviceFactory): Consumer
    {
        $this->serviceFactory = $serviceFactory;

        return $this;
    }

    /**
     * @return \Micronative\EventSchema\Event\EventValidator
     */
    public function getEventValidator(): EventValidator
    {
        return $this->eventValidator;
    }

    /**
     * @param \Micronative\EventSchema\Event\EventValidator $eventValidator
     * @return \Micronative\EventSchema\Consumer
     */
    public function setEventValidator(EventValidator $eventValidator): Consumer
    {
        $this->eventValidator = $eventValidator;

        return $this;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param \Psr\Container\ContainerInterface|null $container
     * @return \Micronative\EventSchema\Consumer
     */
    public function setContainer(?ContainerInterface $container = null): Consumer
    {
        $this->container = $container;

        return $this;
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
     * @return \Micronative\EventSchema\Consumer
     */
    public function setAssetDir(?string $assetDir): Consumer
    {
        $this->assetDir = $assetDir;

        return $this;
    }
}
