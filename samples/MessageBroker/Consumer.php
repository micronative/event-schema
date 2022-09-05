<?php

namespace Samples\MessageBroker;

class Consumer implements ConsumerInterface
{
    protected Broker $broker;

    /**
     * Consumer constructor.
     * @param \Samples\MessageBroker\Broker $broker
     */
    public function __construct(Broker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * @param string $topic
     * @return null
     */
    public function consume(string $topic)
    {
        return $this->broker->pull($topic);
    }
}
