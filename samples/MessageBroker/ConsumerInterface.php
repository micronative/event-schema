<?php

namespace Samples\MessageBroker;

interface ConsumerInterface
{
    public function consume(string $topic);
}
