<?php

namespace Micronative\EventSchema\Config\Consumer;

class ServiceConfig
{
    protected string $class;
    protected ?string $alias;
    protected ?array $callbacks;

    /**
     * ServiceConfig constructor.
     * @param string $class
     * @param string|null $alias
     * @param array|null $callbacks
     */
    public function __construct(string $class, string $alias = null, array $callbacks = null)
    {
        $this->class = $class;
        $this->alias = $alias;
        $this->callbacks = $callbacks;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfig
     */
    public function setClass(string $class): ServiceConfig
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfig
     */
    public function setAlias(string $alias): ServiceConfig
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getCallbacks(): ?array
    {
        return $this->callbacks;
    }

    /**
     * @param string[] $callbacks
     * @return \Micronative\EventSchema\Config\Consumer\ServiceConfig
     */
    public function setCallbacks(array $callbacks): ServiceConfig
    {
        $this->callbacks = $callbacks;

        return $this;
    }
}
