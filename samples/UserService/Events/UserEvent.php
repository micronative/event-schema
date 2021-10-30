<?php

namespace Samples\UserService\Events;

use Micronative\EventSchema\Event\AbstractEvent;

class UserEvent extends AbstractEvent
{
    private \DateTime $createdAt;

    public function __construct(string $name, string $version = null, string $id = null, array $payload = null)
    {
        $this->name = $name;
        $this->version = $version;
        $this->id = $id;
        $this->payload = $payload;
        $this->createdAt = new \DateTime();
    }

    /**
     * @return false|string
     */
    public function jsonSerialize()
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
    public function unserialize(string $jsonString)
    {
        $data = json_decode($jsonString, true);
        $this->name = isset($data['name']) ? $data['name'] : null;
        $this->version = isset($data['version']) ? $data['version'] : null;
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->payload = isset($data['payload']) ? $data['payload'] : null;

        return $this;
    }


}
