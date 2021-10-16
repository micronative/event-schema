<?php

namespace Samples\TaskService;

use Micronative\EventSchema\Consumer;
use Samples\MessageBroker\MockBroker;
use Samples\TaskService\Broadcast\MockReceiver;
use Samples\TaskService\Events\TaskEvent;

class TaskApp
{
    /** @var \Samples\TaskService\Broadcast\MockReceiver */
    private $receiver;

    /** @var \Micronative\EventSchema\Consumer */
    private $consumer;

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
        $this->consumer = new Consumer(
            $assetDir,
            ["/assets/configs/events.yml"],
            ["/assets/configs/services.yml"],
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
            $obj = json_decode($message);
            $taskEvent = new TaskEvent($obj->name, $obj->version, $obj->id, (array)$obj->payload);
            $this->consumer->process($taskEvent);
        }
    }
}
