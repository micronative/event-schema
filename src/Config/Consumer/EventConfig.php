<?php

namespace Micronative\EventSchema\Config\Consumer;

use Micronative\EventSchema\Config\AbstractEventConfig;

class EventConfig extends AbstractEventConfig
{
    protected ?array $serviceClasses;

    /**
     * EventConfig constructor.
     * @param string $name
     * @param string|null $version
     * @param string|null $schemaFile
     * @param array|null $services
     */
    public function __construct(string $name, ?string $version = null, ?string $schemaFile = null, ?array $services = null)
    {
        $this->name = $name;
        $this->version = $version;
        $this->schemaFile = $schemaFile;
        $this->serviceClasses = $services;
    }

    /**
     * @return string[]|null
     */
    public function getServiceClasses(): ?array
    {
        return $this->serviceClasses;
    }

    /**
     * @param string[] $serviceClasses
     * @return \Micronative\EventSchema\Config\Consumer\EventConfig
     */
    public function setServiceClasses(array $serviceClasses): EventConfig
    {
        $this->serviceClasses = $serviceClasses;

        return $this;
    }
}
