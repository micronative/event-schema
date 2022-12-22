<?php

namespace Tests\Event;

use Micronative\EventSchema\Event\AbstractEvent;

class SampleEvent extends AbstractEvent
{
    public function __construct(string $name, string $version = null, string $id = null, array $payload = null)
    {
        $this->name = $name;
        $this->version = $version;
        $this->id = $id;
        $this->payload = $payload;
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode(
            [
                "name" => $this->name,
                "id" => $this->id,
                "payload" => $this->payload
            ]
        );
    }

    /**
     * @param string $jsonString
     * @return \Tests\Event\SampleEvent
     */
    public function fromJson(string $jsonString): AbstractEvent
    {
        $data = json_decode($jsonString, true);
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->payload = isset($data['payload']) ? $data['payload'] : null;

        return $this;
    }
}
