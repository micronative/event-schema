<?php

namespace Samples\UserService\Broadcast;

interface PublisherInterface
{
    public function publish(string $message): bool;
}
