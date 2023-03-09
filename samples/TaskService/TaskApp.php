<?php

namespace Samples\TaskService;

use Micronative\EventSchema\Processor;
use Micronative\MockBroker\Broker;
use Micronative\MockBroker\Consumer;
use Micronative\MockBroker\ConsumerInterface;
use Psr\Container\ContainerInterface;
use Samples\TaskService\Events\TaskEvent;

class TaskApp
{
    const USER_EVENT_TOPIC = 'User.Events';
    private ConsumerInterface $consumer;
    private Processor $processor;

    /**
     * App constructor.
     * @param \Micronative\MockBroker\Broker|null $broker
     * @param \Psr\Container\ContainerInterface|null $container
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(Broker $broker = null, ContainerInterface $container = null)
    {
        $this->consumer = new Consumer($broker);
        $this->processor = new Processor(
            dirname(__FILE__),
            ["/assets/configs/in_events.yml"],
            ["/assets/configs/services.yml"],
            $container
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ProcessorException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function listen()
    {
        $message = $this->consumer->consume(self::USER_EVENT_TOPIC);
        if (!empty($message)) {
            $taskEvent = (new TaskEvent())->fromJson($message);
            echo "-- Start processing incoming event: {$taskEvent->getName()}" . PHP_EOL;
            $this->processor->process($taskEvent);
            echo "-- Finish processing incoming event: {$taskEvent->getName()}" . PHP_EOL;
        }
    }
}
