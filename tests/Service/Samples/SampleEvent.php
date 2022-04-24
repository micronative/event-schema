<?php

namespace Tests\Service\Samples;

use Micronative\EventSchema\Event\AbstractEvent;

class SampleEvent extends AbstractEvent
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
     * @return \Tests\Service\Samples\SampleEvent
     */
    public function fromJson(string $jsonString)
    {
        $jsonObject = json_decode($jsonString);
        $this->name = $jsonObject->name;
        $this->id = $jsonObject->id ?? null;
        $this->payload = (array)$jsonObject->payload ?? null;

        return $this;
    }
}
