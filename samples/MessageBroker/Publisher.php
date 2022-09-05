<?php

namespace Samples\MessageBroker;

class Publisher implements PublisherInterface
{
    protected Broker $broker;

    /**
     * Publisher constructor.
     * @param \Samples\MessageBroker\Broker $broker
     */
    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * @param string $message
     * @param string $topic
     * @return bool
     */
    public function publish(string $message, string $topic)
    {
        return $this->broker->push($message, $topic);
    }
}
