<?php

namespace Samples\MessageBroker;

class MockBroker
{
    /** @var string[] */
    private $messages = [];

    /**
     * @param string $message
     */
    public function push(string $message)
    {
        array_push($this->messages, $message);
    }

    /**
     * @return string
     */
    public function shift()
    {
        return array_shift($this->messages);
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param string[] $messages
     * @return \Samples\MessageBroker\MockBroker
     */
    public function setMessages(array $messages): MockBroker
    {
        $this->messages = $messages;

        return $this;
    }
}
