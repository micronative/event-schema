<?php

namespace Micronative\EventSchema\Config;

abstract class AbstractEventConfig
{
    /** @var string */
    protected $name;

    /** @var string|array */
    protected $version;

    /** @var string */
    protected $schemaFile;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return \Micronative\EventSchema\Config\AbstractEventConfig
     */
    public function setName(string $name): AbstractEventConfig
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|array|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string|array $version
     * @return \Micronative\EventSchema\Config\AbstractEventConfig
     */
    public function setVersion($version): AbstractEventConfig
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSchemaFile()
    {
        return $this->schemaFile;
    }

    /**
     * @param string $schemaFile
     * @return \Micronative\EventSchema\Config\AbstractEventConfig
     */
    public function setSchemaFile(string $schemaFile): AbstractEventConfig
    {
        $this->schemaFile = $schemaFile;

        return $this;
    }
}
