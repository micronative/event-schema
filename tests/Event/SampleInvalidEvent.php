<?php

namespace Tests\Event;

use Micronative\EventSchema\Event\AbstractEvent;

class SampleInvalidEvent extends AbstractEvent
{
    public function __construct(string $name, string $id = null, array $payload = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->payload = $payload;
    }

    /**
     * @return false|string
     */
    public function jsonSerialize()
    {
        return 'invalid_json_string';
    }

    /**
     * @param string $jsonString
     * @return \Tests\Event\SampleInvalidEvent
     */
    public function unserialize(string $jsonString)
    {
        $data = json_decode($jsonString, true);
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->payload = isset($data['payload']) ? $data['payload'] : null;

        return $this;
    }
}
