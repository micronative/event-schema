<?php

namespace Micronative\EventSchema\Event;

abstract class AbstractEvent
{
    protected ?string $name = null;
    protected ?string $version = null;
    protected ?string $id = null;
    protected ?array $payload = null;

    /**
     * @var string|null relative path (from Processor::schemaDir) to json schema file
     * @see \Micronative\EventSchema\Processor::assetDir
     */
    protected ?string $schemaFile = null;

    /**
     * Get the json string representing the event
     * name is required
     * should remove the empty values
     * @return false|string
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     */
    abstract public function toJson();
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
    abstract public function fromJson(string $jsonString);
    /**
     * {
     *   $data = json_decode($jsonString, true);
     *   $this->name = isset($data['name']) ? $data['name'] : null;
     *   $this->version = isset($data['version']) ? $data['version'] : null;
     *   $this->id = isset($data['id']) ? $data['id'] : null;
     *   $this->payload = isset($data['payload']) ? (array)$data['payload'] : null;
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
    public function setId(?string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Micronative\EventSchema\Event\AbstractEvent
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
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
    public function getPayload(): ?array
    {
        return $this->payload;
    }

    /**
     * @param array|null $payload
     * @return \Micronative\EventSchema\Event\AbstractEvent
     */
    public function setPayload(?array $payload = null)
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
