<?php

namespace Micronative\EventSchema\Config\Producer;

use Micronative\EventSchema\Config\AbstractEventConfig;

class EventConfig extends AbstractEventConfig
{
    /**
     * EventConfig constructor.
     * @param string $name
     * @param string|null $version
     * @param string|null $schemaFile
     */
    public function __construct(string $name, string $version = null, string $schemaFile = null)
    {
        $this->name = $name;
        $this->version = $version;
        $this->schemaFile = $schemaFile;
    }


}
