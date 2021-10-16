<?php

namespace Micronative\EventSchema\Service;

use Psr\Container\ContainerInterface;

abstract class AbstractService implements ServiceInterface
{
    /** @var string */
    protected $name;

    /**
     * @var string relative path (from Processor::schemaDir) to json schema file
     * @see \Micronative\EventSchema\Consumer::assetDir
     */
    protected $schema;

    /** @var \Psr\Container\ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param string|null $schema
     * @return \Micronative\EventSchema\Service\AbstractService
     */
    public function setSchema(string $schema = null)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return \Micronative\EventSchema\Service\AbstractService
     */
    public function setName(string $name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param \Psr\Container\ContainerInterface|null $container
     * @return AbstractService
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }
}
