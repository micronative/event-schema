<?php

namespace Samples\TaskService;

use Micronative\EventSchema\Consumer;
use Samples\MessageBroker\MockBroker;
use Samples\TaskService\Broadcast\MockReceiver;
use Samples\TaskService\Events\TaskEvent;

class TaskApp
{
    private MockReceiver $receiver;
    private Consumer $consumer;

    /**
     * App constructor.
     * @param \Samples\MessageBroker\MockBroker|null $broker
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    public function __construct(MockBroker $broker = null)
    {
        $this->receiver = new MockReceiver($broker);
        $assetDir = dirname(__FILE__);
        $container = new Container();
        $this->consumer = new Consumer(
            $assetDir,
            ["/assets/configs/events.yml"],
            ["/assets/configs/services.yml"],
            $container
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\ConsumerException
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     * @throws \Micronative\EventSchema\Exceptions\ValidatorException
     */
    public function listen()
    {
        $message = $this->receiver->get();
        if (!empty($message)) {
            $taskEvent = (new TaskEvent())->unserialize($message);
            echo "-- Start processing event: {$taskEvent->getName()}". PHP_EOL;
            $this->consumer->process($taskEvent);
            echo "-- Finish processing event: {$taskEvent->getName()}". PHP_EOL;
        }
    }
}
