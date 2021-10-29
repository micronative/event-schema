<?php

namespace Micronative\EventSchema\Service;

use Psr\Container\ContainerInterface;

abstract class AbstractService implements ServiceInterface
{
    protected ?string $name = null;
    protected ?ContainerInterface $container;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
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
    public function setName(?string $name)
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
    public function setContainer(?ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }
}
