<?php

namespace Samples\TaskService\Broadcast;

use Samples\MessageBroker\MockBroker;

class MockReceiver
{
    /** @var \Samples\MessageBroker\MockBroker */
    private $broker;

    public function __construct(MockBroker $broker = null)
    {
        $this->broker = $broker;
    }

    public function get()
    {
        return $this->broker->shift();
    }
}
