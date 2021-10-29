<?php

namespace Tests\Config;

use Micronative\EventSchema\Config\Consumer\ServiceConfig;
use Micronative\EventSchema\Config\Consumer\ServiceConfigRegister;
use Micronative\EventSchema\Exceptions\ConfigException;
use PHPUnit\Framework\TestCase;

class ServiceConfigRegisterTest extends TestCase
{
    /** @coversDefaultClass \Micronative\EventSchema\Config\Consumer\ServiceConfigRegister */
    protected ServiceConfigRegister $serviceConfigRegister;
    protected string $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->testDir = dirname(dirname(dirname(__FILE__)));
        $this->serviceConfigRegister = new ServiceConfigRegister(
            $this->testDir,
            ["/assets/consumer/configs/services.yml"]
        );
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testLoadServicesWithEmptyConfigs()
    {
        $this->serviceConfigRegister->setConfigFiles(null);
        $this->serviceConfigRegister->loadServiceConfigs();
        $this->assertEquals([], $this->serviceConfigRegister->getServiceConfigs());
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testLoadServicesWithUnsupportedFile()
    {
        $this->serviceConfigRegister->setConfigFiles([$this->testDir . "/assets/configs/services.csv"]);
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage(ConfigException::UNSUPPORTED_FILE_FORMAT . 'csv');
        $this->serviceConfigRegister->loadServiceConfigs();
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testLoadServices()
    {
        $this->serviceConfigRegister->loadServiceConfigs();
        $services = $this->serviceConfigRegister->getServiceConfigs();

        $this->assertTrue(is_array($services));
        $this->assertTrue(isset($services["Tests\Service\Samples\CreateContact"]));
        $this->assertTrue(isset($services["Tests\Service\Samples\UpdateContact"]));
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testRegisterServiceConfigs()
    {
        $this->serviceConfigRegister->loadServiceConfigs();
        $config = new ServiceConfig("Service.Name", "serviceName", ["SomeServiceSchema"]);
        $this->serviceConfigRegister->registerServiceConfig($config);
        $serviceConfigs = $this->serviceConfigRegister->getServiceConfigs();
        $serviceConfig = $serviceConfigs['Service.Name'];

        $this->assertIsArray($serviceConfigs);
        $this->assertArrayHasKey("Service.Name", $serviceConfigs);
        $this->assertInstanceOf(ServiceConfig::class, $serviceConfig);
        $this->assertEquals('Service.Name', $serviceConfig->getClass());
        $this->assertEquals('serviceName', $serviceConfig->getAlias());
        $this->assertEquals(["SomeServiceSchema"], $serviceConfig->getCallbacks());
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testRetrieveServiceConfig()
    {
        $this->serviceConfigRegister->loadServiceConfigs();
        $config = new ServiceConfig("Service.Name", "serviceName", ["SomeServiceSchema"]);
        $this->serviceConfigRegister->registerServiceConfig($config);
        $serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig('Service.Name');

        $this->assertInstanceOf(ServiceConfig::class, $serviceConfig);
        $this->assertEquals('Service.Name', $serviceConfig->getClass());
        $this->assertEquals('serviceName', $serviceConfig->getAlias());
        $this->assertEquals(["SomeServiceSchema"], $serviceConfig->getCallbacks());
    }

    /**
     * @throws \Micronative\EventSchema\Exceptions\JsonException
     * @throws \Micronative\EventSchema\Exceptions\ConfigException
     */
    public function testRetrieveServiceConfigByAlias()
    {
        $this->serviceConfigRegister->loadServiceConfigs();
        $config = new ServiceConfig("Service.Name", "serviceNameAlias", ["SomeServiceSchema"]);
        $this->serviceConfigRegister->registerServiceConfig($config);
        $serviceConfig = $this->serviceConfigRegister->retrieveServiceConfig('serviceNameAlias');
        $noneExistingConfig = $this->serviceConfigRegister->retrieveServiceConfig('noneExistingConfig');

        $this->assertInstanceOf(ServiceConfig::class, $serviceConfig);
        $this->assertEquals('Service.Name', $serviceConfig->getClass());
        $this->assertEquals('serviceNameAlias', $serviceConfig->getAlias());
        $this->assertEquals(["SomeServiceSchema"], $serviceConfig->getCallbacks());
        $this->assertNull($noneExistingConfig);
    }

    public function testSettersAndGetters()
    {
        $configs = [];
        $this->serviceConfigRegister->setConfigFiles($configs);
        $this->assertEquals($configs, $this->serviceConfigRegister->getConfigFiles());

        $services = [];
        $this->serviceConfigRegister->setServiceConfigs($services);
        $this->assertEquals($services, $this->serviceConfigRegister->getServiceConfigs());

        $assetDir = dirname(dirname(dirname(__FILE__)));
        $this->serviceConfigRegister->setAssetDir($assetDir);
        $this->assertEquals($assetDir, $this->serviceConfigRegister->getAssetDir());
    }
}
