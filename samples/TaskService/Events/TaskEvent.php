<?php

namespace Samples\TaskService\Events;

use Micronative\EventSchema\Event\AbstractEvent;

class TaskEvent extends AbstractEvent
{
    private \DateTime $receivedAt;

    public function __construct(
        ?string $name = null,
        ?string $version = null,
        ?string $id = null,
        ?array $payload = null
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->id = $id;
        $this->payload = $payload;
        $this->receivedAt = new \DateTime();
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode(
            array_filter(
                [
                    "name" => $this->name,
                    "version" => $this->version,
                    "id" => $this->id,
                    "payload" => $this->payload,
                    "received_at" => $this->receivedAt->format('Y-m-d H:i:s')
                ]
            )
        );
    }

    /**
     * @param string $jsonString
     * @return \Samples\TaskService\Events\TaskEvent
     */
    public function fromJson(string $jsonString)
    {
        $data = json_decode($jsonString, true);
        $this->name = $data['name'] ?? null;
        $this->id = $data['id'] ?? null;
        $this->payload = $data['payload'] ?? null;

        return $this;
    }
}
