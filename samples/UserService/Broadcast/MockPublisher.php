<?php

namespace Samples\UserService\Broadcast;

use Samples\MessageBroker\MockBroker;

class MockPublisher implements PublisherInterface
{
    private ?MockBroker $broker;

    public function __construct(MockBroker $broker = null)
    {
        $this->broker = $broker;
    }

    public function publish(string $message): bool
    {
        $this->broker->push($message);

        return true;
    }
}
