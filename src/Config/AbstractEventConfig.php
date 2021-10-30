<?php

namespace Micronative\EventSchema\Config;

abstract class AbstractEventConfig
{
    protected string $name;
    protected ?string $version;
    protected ?string $schemaFile;

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
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     * @return \Micronative\EventSchema\Config\AbstractEventConfig
     */
    public function setVersion(?string $version): AbstractEventConfig
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
