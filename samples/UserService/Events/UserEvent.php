<?php

namespace Samples\UserService\Events;

use Micronative\EventSchema\Event\AbstractEvent;

class UserEvent extends AbstractEvent
{
    const USER_EVENT_TOPIC = 'User.Events';
    const USER_CREATED = 'User.Created';
    const USER_UPDATED = 'User.Updated';
    const VERSION = "1.0.0";

    private \DateTime $createdAt;

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
        $this->createdAt = new \DateTime();
    }

    /**
     * @return false|string
     */
    public function toJson()
    {
        return json_encode(
            array_filter(
                [
                    'name' => $this->name,
                    'version' => $this->version,
                    'id' => $this->id,
                    'payload' => $this->payload,
                    'created_at' => $this->createdAt->format('Y-m-d H:i:s')
                ]
            )
        );
    }

    /**
     * @param string $jsonString
     * @return \Samples\UserService\Events\UserEvent
     */
    public function fromJson(string $jsonString): UserEvent
    {
        $data = json_decode($jsonString, true);
        $this->name = $data['name'] ?? null;
        $this->version = $data['version'] ?? null;
        $this->id = $data['id'] ?? null;
        $this->payload = $data['payload'] ?? null;

        return $this;
    }

}
