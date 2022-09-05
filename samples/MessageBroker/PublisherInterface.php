<?php

namespace Samples\MessageBroker;

interface PublisherInterface
{
    public function publish(string $message, string $topic);
}
