<?php

namespace Micronative\EventSchema\Service;

use Micronative\EventSchema\Config\Consumer\ServiceConfig;
use Micronative\EventSchema\Exceptions\ServiceException;
use Psr\Container\ContainerInterface;

class ServiceFactory
{
    /**
     * @param \Micronative\EventSchema\Config\Consumer\ServiceConfig $serviceConfig
     * @param \Psr\Container\ContainerInterface|null $container
     * @return false|\Micronative\EventSchema\Service\ServiceInterface
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     */
    public function createService(ServiceConfig $serviceConfig, ContainerInterface $container = null)
    {
        $serviceClass = $serviceConfig->getClass();
        try {
            $service = new $serviceClass($container);
        } catch (\Error $exception) {
            throw new ServiceException(ServiceException::INVALID_SERVICE_CLASS . $serviceClass);
        }

        if ($service instanceof ServiceInterface) {
            return $service;
        }

        return false;
    }
}
