<?php

namespace Micronative\EventSchema\Config\Consumer;

use Micronative\EventSchema\Config\AbstractEventConfig;

class EventConfig extends AbstractEventConfig
{
    /** @var string[] */
    protected $serviceClasses;

    /**
     * EventConfig constructor.
     * @param string $name
     * @param string|array|null $version
     * @param string|null $schemaFile
     * @param array|null $services
     */
    public function __construct(string $name, $version = null, string $schemaFile = null, array $services = null)
    {
        if (is_string($version)) {
            $version = [$version];
        }
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
