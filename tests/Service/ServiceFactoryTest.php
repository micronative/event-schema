<?php

namespace Tests\Service;

use Micronative\EventSchema\Config\Consumer\ServiceConfig;
use Micronative\EventSchema\Exceptions\ServiceException;
use Micronative\EventSchema\Service\ServiceFactory;
use Micronative\EventSchema\Service\ServiceInterface;
use PHPUnit\Framework\TestCase;

class ServiceFactoryTest extends TestCase
{
    protected string $testDir;
    protected ServiceFactory $serviceFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(__FILE__));
        $this->serviceFactory = new ServiceFactory();
    }

    /**
     * @covers \Micronative\EventSchema\Service\ServiceFactory::createService
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     */
    public function testCreateInvalidServiceClass()
    {
        $serviceClass = "\Tests\Service\Samples\InvalidServiceClass";
        $serviceConfig = new ServiceConfig($serviceClass, null, []);
        $this->expectException(ServiceException::class);
        $this->serviceFactory->createService($serviceConfig);
    }

    /**
     * @covers \Micronative\EventSchema\Service\ServiceFactory::createService
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     */
    public function testCreateInvalidService()
    {
        $serviceClass = "\Tests\Service\Samples\InvalidService";
        $serviceConfig = new ServiceConfig($serviceClass, null, []);
        $service = $this->serviceFactory->createService($serviceConfig);
        $this->assertFalse($service);
    }

    /**
     * @covers \Micronative\EventSchema\Service\ServiceFactory::createService
     * @throws \Micronative\EventSchema\Exceptions\ServiceException
     */
    public function testCreateService()
    {
        $serviceClass = "\Tests\Service\Samples\CreateContact";
        $serviceConfig = new ServiceConfig($serviceClass, null, []);
        $service = $this->serviceFactory->createService($serviceConfig);
        $this->assertTrue($service instanceof ServiceInterface);
    }
}
