<?php

namespace Samples\TaskService;

use Micronative\EventSchema\Processor;
use Micronative\MockBroker\Broker;
use Micronative\MockBroker\Consumer;
use Micronative\MockBroker\ConsumerInterface;
use Samples\TaskService\Events\TaskEvent;

class TaskApp
{
    const USER_EVENT_TOPIC = 'User.Events';
    private ConsumerInterface $consumer;
    private Processor $processor;

    /**
     * App constructor.
     * @param \Micronative\MockBroker\Broker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(Broker $broker = null)
    {
        $this->consumer = new Consumer($broker);
        $container = new Container();
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
