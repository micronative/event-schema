<?php

namespace Samples\TaskService\Broadcast;

use Samples\MessageBroker\MockBroker;

class MockReceiver
{
    private ?MockBroker $broker;

    public function __construct(MockBroker $broker = null)
    {
        $this->broker = $broker;
    }

    public function get()
    {
        return $this->broker->shift();
    }
}
