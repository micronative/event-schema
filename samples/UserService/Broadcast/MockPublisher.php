<?php

namespace Samples\UserService\Broadcast;

use Samples\MessageBroker\MockBroker;

class MockPublisher
{
    /** @var \Samples\MessageBroker\MockBroker */
    private $broker;

    public function __construct(MockBroker $broker = null)
    {
        $this->broker = $broker;
    }

    public function publish(string $message)
    {
        $this->broker->push($message);
    }
}
