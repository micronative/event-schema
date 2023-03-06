<?php

namespace Samples\UserService\Messaging;

use Samples\UserService\Events\UserEvent;

interface PublisherInterface
{
    public function publishEvent(UserEvent $userEvent);
}