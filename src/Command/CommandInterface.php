<?php

namespace Micronative\EventSchema\Command;

interface CommandInterface
{
    /**
     * @return \Micronative\EventSchema\Event\AbstractEvent|bool
     */
    public function execute();
}
