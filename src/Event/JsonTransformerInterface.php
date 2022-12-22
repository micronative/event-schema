<?php

namespace Micronative\EventSchema\Event;

interface JsonTransformerInterface
{
    /**
     * @return false|string
     */
    public function toJson();

    /**
     * @param string $jsonString
     * @return \Micronative\EventSchema\Event\AbstractEvent
     */
    public function fromJson(string $jsonString): AbstractEvent;
}