<?php

namespace Samples\UserService\Broadcast;

use Samples\MessageBroker\MockBroker;

class MockPublisher
{
    private ?MockBroker $broker;

    public function __construct(MockBroker $broker = null)
    {
        $this->broker = $broker;
    }

    public function publish(string $message)
    {
        $this->broker->push($message);
    }
}
