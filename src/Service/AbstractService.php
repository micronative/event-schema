<?php

namespace Micronative\EventSchema\Service;

use Psr\Container\ContainerInterface;

abstract class AbstractService implements ServiceInterface
{
    protected ?ContainerInterface $container = null;

    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
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
