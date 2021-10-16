<?php

namespace Micronative\EventSchema\Event;

use JsonSerializable;

abstract class AbstractEvent implements JsonSerializable
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $version;

    /** @var string|null */
    protected $id;

    /** @var array|null */
    protected $payload;

    /**
     * @var string relative path (from Processor::schemaDir) to json schema file
     * @see \Micronative\EventSchema\Consumer::assetDir
     */
    protected $schemaFile;

    /**
     * Get the json string representing the event
     * name is required
     * should remove the empty values
     * @return false|string
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    abstract public function jsonSerialize();
    /**
     * {
     *   return json_encode(
     *     array_filter([
     *       "name" => $this->name,
     *       "id" => $this->id,
     *       "payload" => $this->payload,
     *     ])
     *   );
     * }
     */

    /**
     * Set event properties from json string
     * @param string $jsonString
     * @return \Micronative\EventSchema\Event\AbstractEvent
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    abstract public function unserialize(string $jsonString);
    /**
     * {
     *   $data = json_decode($jsonString, true);
     *   $this->name = isset($data['name']) ? $data['name'] : null;
     *   $this->version = isset($data['version']) ? $data['version'] : null;
     *   $this->id = isset($data['id']) ? $data['id'] : null;
     *   $this->payload = isset($data['payload']) ? $data['payload'] : null;
     *
     *   return $this;
     * }
     */

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return \Micronative\EventSchema\Event\AbstractEvent
     */
    public function setId(string $id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return \Micronative\EventSchema\Event\AbstractEvent
     */
    public function setName(string $name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return \Micronative\EventSchema\Event\AbstractEvent
     */
    public function setVersion(string $version): AbstractEvent
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @param array|null $payload
     * @return \Micronative\EventSchema\Event\AbstractEvent
     */
    public function setPayload($payload = null)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSchemaFile(): ?string
    {
        return $this->schemaFile;
    }

    /**
     * @param string|null $schemaFile
     * @return \Micronative\EventSchema\Event\AbstractEvent
     */
    public function setSchemaFile(?string $schemaFile): AbstractEvent
    {
        $this->schemaFile = $schemaFile;

        return $this;
    }
}
